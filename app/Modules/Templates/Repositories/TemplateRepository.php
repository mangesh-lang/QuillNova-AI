<?php

namespace App\Modules\Templates\Repositories;

use App\Models\Template;
use App\Models\Category;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class TemplateRepository extends BaseRepository
{
    public function __construct(Template $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active templates by category slug.
     */
    public function getByCategory(string $categorySlug): Collection
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug)->where('is_active', true);
            })
            ->with('category')
            ->get();
    }

    /**
     * Get all active categories with their active templates.
     */
    public function getActiveCategoriesWithTemplates(): Collection
    {
        return Category::where('is_active', true)
            ->with(['templates' => function($q) {
                $q->where('is_active', true);
            }])
            ->get();
    }

    /**
     * Search active templates.
     */
    public function searchTemplates(string $query): Collection
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->get();
    }
}
