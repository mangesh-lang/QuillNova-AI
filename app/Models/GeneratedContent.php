<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedContent extends Model
{
    protected $table = 'generated_contents';

    protected $fillable = [
        'user_id',
        'template_id',
        'title',
        'prompt_text',
        'result_text',
        'word_count',
        'tool_type',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
        'word_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
