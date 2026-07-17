<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLimit extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'request_count',
        'word_count',
    ];

    protected $casts = [
        'date' => 'date',
        'request_count' => 'integer',
        'word_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
