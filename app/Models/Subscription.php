<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_name',
        'request_limit',
        'used_requests',
        'reset_at',
    ];

    protected $casts = [
        'reset_at'      => 'datetime',
        'request_limit' => 'integer',
        'used_requests' => 'integer',
    ];

    public const PLANS = [
        'free' => 20,
        'pro'  => 500,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasReachedLimit(): bool
    {
        return $this->used_requests >= $this->request_limit;
    }

    public function remainingRequests(): int
    {
        return max(0, $this->request_limit - $this->used_requests);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_requests');
    }

    public function upgradeToPro(): void
    {
        $this->update([
            'plan_name'     => 'pro',
            'request_limit' => self::PLANS['pro'],
        ]);
    }
}
