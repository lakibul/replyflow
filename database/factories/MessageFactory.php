<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'message_text' => $this->faker->paragraph(),
            'tone'         => $this->faker->randomElement(['professional', 'friendly', 'formal', 'empathetic']),
            'status'       => 'pending',
            'ai_reply'     => null,
            'summary'      => null,
            'category'     => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status'   => 'completed',
            'ai_reply' => $this->faker->paragraph(),
            'summary'  => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['Billing', 'Technical', 'General']),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status'        => 'failed',
            'error_message' => 'AI processing failed: API error.',
        ]);
    }
}
