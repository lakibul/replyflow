<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'created_at' => $this->created_at->toIso8601String(),
            'subscription' => $this->whenLoaded('subscription', fn () => [
                'plan'      => $this->subscription->plan_name,
                'limit'     => $this->subscription->request_limit,
                'used'      => $this->subscription->used_requests,
                'remaining' => $this->subscription->remainingRequests(),
            ]),
        ];
    }
}
