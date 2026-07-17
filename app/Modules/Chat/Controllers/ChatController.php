<?php

namespace App\Modules\Chat\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\ActivityLog;
use App\Services\AIEngineService;
use App\Services\DailyLimitService;
use App\Services\SettingService;
use App\Modules\Chat\Repositories\ChatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected ChatRepository $chatRepo;
    protected AIEngineService $aiEngine;
    protected DailyLimitService $limitService;
    protected SettingService $settings;

    public function __construct(
        ChatRepository $chatRepo,
        AIEngineService $aiEngine,
        DailyLimitService $limitService,
        SettingService $settings
    ) {
        $this->chatRepo = $chatRepo;
        $this->aiEngine = $aiEngine;
        $this->limitService = $limitService;
        $this->settings = $settings;
    }

    /**
     * Display the AI Chat interface.
     */
    public function index(?int $id = null)
    {
        $user = Auth::user();
        $sessions = $this->chatRepo->getUserSessions($user->id);
        
        $activeSession = null;
        $messages = collect();

        if ($id) {
            $activeSession = ChatSession::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $messages = $this->chatRepo->getSessionMessages($id, $user->id);
        } elseif ($sessions->isNotEmpty()) {
            // Redirect to the latest chat session automatically
            return redirect()->route('chat.show', ['id' => $sessions->first()->id]);
        }

        return view('chat.index', compact('sessions', 'activeSession', 'messages'));
    }

    /**
     * Create a new chat session.
     */
    public function createSession(Request $request)
    {
        $user = Auth::user();
        
        $session = ChatSession::create([
            'user_id' => $user->id,
            'title' => 'New Conversation',
            'model' => $request->input('model', $this->settings->get('default_ai_provider', 'openai')),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'chat_create',
            'details' => 'Created chat session #' . $session->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'title' => $session->title,
        ]);
    }

    /**
     * Rename an existing chat session.
     */
    public function renameSession(Request $request, int $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:100',
            'model' => 'nullable|string|in:openai,gemini',
        ]);

        $user = Auth::user();
        $session = ChatSession::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        
        $updateData = [];
        if ($request->has('title')) {
            $updateData['title'] = $request->title;
        }
        if ($request->has('model')) {
            $updateData['model'] = $request->model;
        }

        $session->update($updateData);

        return response()->json([
            'success' => true,
            'title' => $session->title,
            'model' => $session->model,
        ]);
    }

    /**
     * Delete an existing chat session.
     */
    public function deleteSession(int $id)
    {
        $user = Auth::user();
        $session = ChatSession::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted successfully.',
        ]);
    }

    /**
     * Send user message and generate AI response.
     */
    public function sendMessage(Request $request, int $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $user = Auth::user();
        $session = ChatSession::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // 1. Verify daily limit system
        if ($this->limitService->hasExceededLimits($user)) {
            return response()->json([
                'success' => false,
                'error' => "You have reached today's free limit. Please come back tomorrow.",
            ], 429);
        }

        // 2. Save user message
        $userMsg = $this->chatRepo->createMessage($session->id, 'user', $request->content);

        // 3. Retrieve past messages for context
        $pastMessages = ChatMessage::where('chat_session_id', $session->id)
            ->orderBy('created_at', 'asc')
            ->get(['role', 'content'])
            ->toArray();

        // 4. Determine AI provider
        $provider = $session->model;

        // 5. Query AI engine
        $aiResult = $this->aiEngine->generateChat($pastMessages, $provider);

        if (!$aiResult['success']) {
            // Delete user message on generation failure so they can retry without saving orphaned messages
            $userMsg->delete();

            return response()->json([
                'success' => false,
                'error' => $aiResult['error'],
            ], 500);
        }

        $assistantText = $aiResult['text'];
        $wordsCount = $aiResult['word_count'];

        // 6. Record limit usage
        $this->limitService->recordUsage($user, $wordsCount);

        // 7. Save assistant message
        $assistantMsg = $this->chatRepo->createMessage($session->id, 'assistant', $assistantText);

        // Update session timestamp
        $session->touch();

        // Auto-rename chat session from first message if title is default "New Conversation"
        if ($session->title === 'New Conversation') {
            $words = explode(' ', trim($request->content));
            $newTitle = implode(' ', array_slice($words, 0, 4));
            if (strlen($newTitle) > 30) {
                $newTitle = substr($newTitle, 0, 27) . '...';
            }
            if (empty($newTitle)) {
                $newTitle = 'Chat Session';
            }
            $session->update(['title' => $newTitle]);
        }

        return response()->json([
            'success' => true,
            'user_message' => $userMsg,
            'assistant_message' => $assistantMsg,
            'session_title' => $session->title,
        ]);
    }
}
