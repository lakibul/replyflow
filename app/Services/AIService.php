<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class AIService
{
    private string $model;

    public function __construct()
    {
        $this->model = config('openai.request.model', 'gpt-4o-mini');
    }

    /**
     * Generate a professional reply to a customer message.
     */
    public function generateReply(string $message, string $tone = 'professional'): string
    {
        $prompt = "You are a professional customer support agent. Write a polite, concise, and helpful reply to the following message. Tone: {$tone}. Message: {$message}";

        return $this->chat($prompt);
    }

    /**
     * Summarize a customer message in 1-2 sentences.
     */
    public function summarizeMessage(string $message): string
    {
        $prompt = "Summarize the following customer issue in 1-2 sentences: {$message}";

        return $this->chat($prompt);
    }

    /**
     * Classify a message into Billing, Technical, or General.
     */
    public function classifyMessage(string $message): string
    {
        $prompt = "Classify the following message into one of these categories: Billing, Technical, General. Respond with only the category name. Message: {$message}";

        $result = $this->chat($prompt);

        // Normalize to ensure only valid categories are returned
        $normalized = ucfirst(strtolower(trim($result)));

        return in_array($normalized, ['Billing', 'Technical', 'General'])
            ? $normalized
            : 'General';
    }

    /**
     * Process all three AI operations in a single API call to avoid rate limits.
     *
     * @return array{reply: string, summary: string, category: string}
     */
    public function processMessage(string $message, string $tone = 'professional'): array
    {
        if (config('app.ai_fake')) {
            return $this->fakeResponse($message, $tone);
        }

        $prompt = <<<PROMPT
You are a professional customer support assistant. Given the customer message below, respond with a JSON object containing exactly these three keys:

1. "reply"    — A polite, concise, helpful reply. Tone: {$tone}.
2. "summary"  — A 1-2 sentence summary of the customer issue.
3. "category" — One of: Billing, Technical, General.

Respond ONLY with valid JSON, no explanation. Example:
{"reply":"...","summary":"...","category":"Billing"}

Customer message: {$message}
PROMPT;

        $raw = $this->chat($prompt);

        // Strip markdown code fences if present
        $json = preg_replace('/^```(?:json)?\s*|\s*```$/s', '', trim($raw));

        $data = json_decode($json, true);

        if (! is_array($data) || ! isset($data['reply'], $data['summary'], $data['category'])) {
            throw new Exception('AI returned invalid JSON: ' . $raw);
        }

        $category = ucfirst(strtolower(trim($data['category'])));

        return [
            'reply'    => trim($data['reply']),
            'summary'  => trim($data['summary']),
            'category' => in_array($category, ['Billing', 'Technical', 'General']) ? $category : 'General',
        ];
    }

    /**
     * Return a realistic fake response for local testing (no API call).
     *
     * @return array{reply: string, summary: string, category: string}
     */
    private function fakeResponse(string $message, string $tone): array
    {
        $categories = ['Billing', 'Technical', 'General'];
        $category   = $categories[crc32($message) % 3];

        $replies = [
            'professional' => "Thank you for reaching out to us. I understand your concern and want to assure you that we take this seriously. Our team will investigate the matter and get back to you within 24 hours with a resolution. We appreciate your patience.",
            'friendly'     => "Hey there! Thanks so much for getting in touch! 😊 I totally get where you're coming from and I'm here to help. Let me look into this for you right away and we'll get it sorted out!",
            'formal'       => "Dear Valued Customer, we acknowledge receipt of your correspondence and wish to assure you that your matter is receiving our utmost attention. We shall revert to you with a comprehensive response at the earliest opportunity.",
            'empathetic'   => "I completely understand how frustrating this must be for you, and I sincerely apologize for the inconvenience. Your experience matters deeply to us. Let me personally make sure this gets resolved for you as quickly as possible.",
            'assertive'    => "We have received your message and are taking immediate action. This issue will be escalated to our senior team and you will receive a definitive resolution within 24 hours. No further delays will occur.",
        ];

        return [
            'reply'    => $replies[$tone] ?? $replies['professional'],
            'summary'  => 'The customer has submitted an inquiry regarding their account. The issue requires follow-up from the support team.',
            'category' => $category,
        ];
    }

    /**
     * Send a chat completion request to OpenAI.
     *
     * @throws Exception
     */
    private function chat(string $prompt): string
    {
        try {
            $response = OpenAI::chat()->create([
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens'  => 500,
                'temperature' => 0.7,
            ]);

            return trim($response->choices[0]->message->content ?? '');
        } catch (Exception $e) {
            Log::error('AIService error', [
                'message' => $e->getMessage(),
                'prompt'  => substr($prompt, 0, 100),
            ]);

            throw new Exception('AI processing failed: ' . $e->getMessage(), 0, $e);
        }
    }
}
