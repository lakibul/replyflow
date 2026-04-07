<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Jobs\ProcessMessageJob;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $messages = $request->user()
            ->messages()
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $user = $request->user();

        // Increment usage and bust subscription cache
        $subscription = $user->getOrCreateSubscription();
        $subscription->incrementUsage();
        Cache::forget("subscription:{$user->id}");

        $message = $user->messages()->create([
            'message_text' => $request->message_text,
            'tone'         => $request->input('tone', 'professional'),
            'status'       => 'pending',
        ]);

        ProcessMessageJob::dispatch($message);

        return response()->json([
            'message' => 'Message submitted. AI processing has started.',
            'data'    => new MessageResource($message),
        ], JsonResponse::HTTP_CREATED);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $message = $request->user()
            ->messages()
            ->findOrFail($id);

        return response()->json([
            'data' => new MessageResource($message),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $message = $request->user()
            ->messages()
            ->findOrFail($id);

        $message->delete();

        return response()->json(['message' => 'Message deleted.']);
    }
}
