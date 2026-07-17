<?php

namespace App\Modules\History\Repositories;

use App\Models\GeneratedContent;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryRepository extends BaseRepository
{
    public function __construct(GeneratedContent $model)
    {
        parent::__construct($model);
    }

    /**
     * Search and filter generated content for a specific user.
     */
    public function getUserHistory(
        int $userId,
        string $search = '',
        string $toolType = '',
        ?bool $isFavorite = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        $q = $this->model->newQuery()
            ->where('user_id', $userId)
            ->with('template');

        if (!empty($search)) {
            $q->where(function($sub) use ($search) {
                $sub->where('title', 'like', "%{$search}%")
                    ->orWhere('result_text', 'like', "%{$search}%")
                    ->orWhere('prompt_text', 'like', "%{$search}%");
            });
        }

        if (!empty($toolType)) {
            $q->where('tool_type', $toolType);
        }

        if ($isFavorite !== null) {
            $q->where('is_favorite', $isFavorite);
        }

        return $q->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Toggle favorite status.
     */
    public function toggleFavorite(int $id, int $userId): ?GeneratedContent
    {
        $content = $this->model->newQuery()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($content) {
            $content->update(['is_favorite' => !$content->is_favorite]);
            return $content;
        }

        return null;
    }
}
