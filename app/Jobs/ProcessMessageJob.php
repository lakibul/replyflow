<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\AIService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    public function __construct(
        private readonly Message $message
    ) {}

    public function handle(AIService $aiService): void
    {
        $this->message->markAsProcessing();

        try {
            $results = $aiService->processMessage(
                $this->message->message_text,
                $this->message->tone
            );

            $this->message->markAsCompleted(
                $results['reply'],
                $results['summary'],
                $results['category']
            );

            Log::info('Message processed successfully', [
                'message_id' => $this->message->id,
                'category'   => $results['category'],
            ]);
        } catch (Exception $e) {
            $this->message->markAsFailed($e->getMessage());

            Log::error('Message processing failed', [
                'message_id' => $this->message->id,
                'error'      => $e->getMessage(),
                'attempt'    => $this->attempts(),
            ]);

            // Re-throw on final attempt to trigger failed() callback
            if ($this->attempts() >= $this->tries) {
                throw $e;
            }
        }
    }

    public function failed(Exception $exception): void
    {
        $this->message->markAsFailed($exception->getMessage());

        Log::error('ProcessMessageJob permanently failed', [
            'message_id' => $this->message->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}
