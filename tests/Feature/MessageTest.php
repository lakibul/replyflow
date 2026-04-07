<?php

namespace Tests\Feature;

use App\Jobs\ProcessMessageJob;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user  = User::factory()->create();
        $this->user->getOrCreateSubscription();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    public function test_unauthenticated_user_cannot_create_message(): void
    {
        $this->postJson('/api/messages', ['message_text' => 'Hello'])
            ->assertStatus(401);
    }

    public function test_user_can_create_message_and_job_is_dispatched(): void
    {
        Queue::fake();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/messages', [
                'message_text' => 'My subscription was charged twice this month.',
                'tone'         => 'professional',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'message_text', 'tone', 'status'],
            ])
            ->assertJsonPath('data.status', 'pending');

        Queue::assertPushed(ProcessMessageJob::class);

        $this->assertDatabaseHas('messages', [
            'user_id' => $this->user->id,
            'status'  => 'pending',
        ]);
    }

    public function test_message_creation_validates_message_text(): void
    {
        Queue::fake();

        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/messages', ['message_text' => 'too short'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message_text']);
    }

    public function test_message_creation_validates_tone(): void
    {
        Queue::fake();

        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/messages', [
                'message_text' => 'Valid message text here that is long enough.',
                'tone'         => 'invalid-tone',
            ])->assertStatus(422)
              ->assertJsonValidationErrors(['tone']);
    }

    public function test_user_can_list_their_messages(): void
    {
        Message::factory()->count(3)->create(['user_id' => $this->user->id]);

        // Another user's message — should not appear
        $other = User::factory()->create();
        Message::factory()->create(['user_id' => $other->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/messages');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_view_single_message(): void
    {
        $message = Message::factory()->create(['user_id' => $this->user->id]);

        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/messages/{$message->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $message->id);
    }

    public function test_user_cannot_view_another_users_message(): void
    {
        $other   = User::factory()->create();
        $message = Message::factory()->create(['user_id' => $other->id]);

        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/messages/{$message->id}")
            ->assertStatus(404);
    }

    public function test_subscription_limit_blocks_request_when_exceeded(): void
    {
        Queue::fake();

        // Set used_requests to the limit
        $this->user->subscription()->update([
            'request_limit' => 20,
            'used_requests' => 20,
        ]);

        $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/messages', [
                'message_text' => 'I cannot send this because limit is reached.',
            ])->assertStatus(429);
    }

    public function test_ai_service_is_called_during_job_processing(): void
    {
        $this->mock(AIService::class, function ($mock) {
            $mock->shouldReceive('processMessage')
                ->once()
                ->andReturn([
                    'reply'    => 'Thank you for reaching out. We have resolved your issue.',
                    'summary'  => 'Customer was charged twice.',
                    'category' => 'Billing',
                ]);
        });

        $message = Message::factory()->create([
            'user_id'      => $this->user->id,
            'message_text' => 'I was charged twice this month.',
            'tone'         => 'professional',
            'status'       => 'pending',
        ]);

        (new ProcessMessageJob($message))->handle(app(AIService::class));

        $message->refresh();

        $this->assertEquals('completed', $message->status);
        $this->assertEquals('Billing', $message->category);
        $this->assertNotNull($message->ai_reply);
        $this->assertNotNull($message->summary);
    }

    public function test_job_marks_message_as_failed_on_ai_error(): void
    {
        $this->mock(AIService::class, function ($mock) {
            $mock->shouldReceive('processMessage')
                ->once()
                ->andThrow(new \Exception('OpenAI API unavailable'));
        });

        $message = Message::factory()->create([
            'user_id' => $this->user->id,
            'status'  => 'pending',
        ]);

        $job = new ProcessMessageJob($message);

        // Simulate the job being on its last attempt
        try {
            $job->handle(app(AIService::class));
        } catch (\Exception) {
            // Expected on final attempt
        }

        $message->refresh();
        $this->assertEquals('failed', $message->status);
        $this->assertNotNull($message->error_message);
    }
}
