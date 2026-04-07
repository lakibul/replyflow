<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'message_text' => $this->message_text,
            'tone'         => $this->tone,
            'status'       => $this->status,
            'result'       => $this->when($this->isCompleted(), [
                'ai_reply' => $this->ai_reply,
                'summary'  => $this->summary,
                'category' => $this->category,
            ]),
            'error'        => $this->when($this->status === 'failed', $this->error_message),
            'created_at'   => $this->created_at->toIso8601String(),
            'updated_at'   => $this->updated_at->toIso8601String(),
        ];
    }
}
