<?php

namespace App\Modules\EmailWriter\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\GeneratedContent;
use App\Models\ActivityLog;
use App\Services\AIEngineService;
use App\Services\DailyLimitService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmailWriterController extends Controller
{
    protected AIEngineService $aiEngine;
    protected DailyLimitService $limitService;
    protected SettingService $settings;

    public function __construct(AIEngineService $aiEngine, DailyLimitService $limitService, SettingService $settings)
    {
        $this->aiEngine = $aiEngine;
        $this->limitService = $limitService;
        $this->settings = $settings;
    }

    /**
     * Show dedicated email composer UI.
     */
    public function index()
    {
        return view('email-writer.index');
    }

    /**
     * Generate email subject & body.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string|max:255',
            'context' => 'required|string',
            'purpose' => 'required|string|max:500',
            'tone' => 'required|string',
            'language' => 'required|string',
        ]);

        $user = Auth::user();
        if ($this->limitService->hasExceededLimits($user)) {
            return response()->json([
                'success' => false,
                'error' => "You have reached today's free limit. Please come back tomorrow.",
            ], 429);
        }

        $prompt = "Write a professional email in {$request->language} language addressed to \"{$request->recipient}\".\n";
        $prompt .= "Purpose of the email: \"{$request->purpose}\".\n";
        $prompt .= "Context/Background: \"{$request->context}\".\n";
        $prompt .= "Use a \"{$request->tone}\" tone. Format the output starting with a clear 'Subject: [Subject Line]' at the top, followed by a double line break, and then write the email body.";

        $provider = $request->input('provider', $this->settings->get('default_ai_provider', 'openai'));
        $result = $this->aiEngine->generate($prompt, $provider);

        if (!$result['success']) {
            return response()->json(['success' => false, 'error' => $result['error']], 500);
        }

        $this->limitService->recordUsage($user, $result['word_count']);

        $template = Template::where('slug', 'email-writer')->first();

        // Save email in history
        $savedContent = GeneratedContent::create([
            'user_id' => $user->id,
            'template_id' => $template ? $template->id : null,
            'title' => 'Email Writer - ' . Str::limit($request->purpose, 30),
            'prompt_text' => $prompt,
            'result_text' => $result['text'],
            'word_count' => $result['word_count'],
            'tool_type' => 'email_writer',
            'is_favorite' => false,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'email_writer',
            'details' => 'Generated email copywriting.',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $savedContent->id,
            'title' => $savedContent->title,
            'result' => $result['text'],
            'word_count' => $result['word_count'],
        ]);
    }
}
