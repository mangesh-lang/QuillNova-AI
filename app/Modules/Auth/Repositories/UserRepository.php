<?php

namespace App\Modules\Auth\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Search and paginate users for Admin dashboard.
     */
    public function searchAndPaginate(string $query = '', string $status = '', int $perPage = 10): LengthAwarePaginator
    {
        $q = $this->model->newQuery()->with('profile', 'roles');

        if (!empty($query)) {
            $q->where(function($sub) use ($query) {
                $sub->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            });
        }

        if (!empty($status)) {
            $q->where('status', $status);
        }

        return $q->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
