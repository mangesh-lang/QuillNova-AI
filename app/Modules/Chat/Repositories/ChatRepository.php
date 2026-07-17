<?php

namespace App\Modules\Chat\Repositories;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository extends BaseRepository
{
    public function __construct(ChatSession $model)
    {
        parent::__construct($model);
    }

    /**
     * Get chat sessions of user.
     */
    public function getUserSessions(int $userId): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Get session messages.
     */
    public function getSessionMessages(int $sessionId, int $userId): Collection
    {
        $session = $this->model->newQuery()
            ->where('id', $sessionId)
            ->where('user_id', $userId)
            ->firstOrFail();

        return $session->messages()->orderBy('created_at', 'asc')->get();
    }

    /**
     * Create chat message.
     */
    public function createMessage(int $sessionId, string $role, string $content): ChatMessage
    {
        return ChatMessage::create([
            'chat_session_id' => $sessionId,
            'role' => $role,
            'content' => $content,
        ]);
    }
}
