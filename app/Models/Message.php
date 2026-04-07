<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_text',
        'tone',
        'ai_reply',
        'summary',
        'category',
        'status',
        'error_message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsCompleted(string $reply, string $summary, string $category): void
    {
        $this->update([
            'ai_reply'  => $reply,
            'summary'   => $summary,
            'category'  => $category,
            'status'    => 'completed',
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status'        => 'failed',
            'error_message' => $error,
        ]);
    }
}
