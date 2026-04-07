<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Jobs\ProcessMessageJob;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $messages = $request->user()
            ->messages()
            ->latest()
            ->paginate(15);

        return view('messages.index', compact('messages'));
    }

    public function create(): View
    {
        return view('messages.create');
    }

    public function store(StoreMessageRequest $request): RedirectResponse
    {
        $user         = $request->user();
        $subscription = $user->getOrCreateSubscription();

        if ($subscription->hasReachedLimit()) {
            return back()->with('error', 'Monthly request limit reached. Please upgrade your plan.');
        }

        $subscription->incrementUsage();
        Cache::forget("subscription:{$user->id}");

        $message = $user->messages()->create([
            'message_text' => $request->message_text,
            'tone'         => $request->input('tone', 'professional'),
            'status'       => 'pending',
        ]);

        ProcessMessageJob::dispatch($message);

        return redirect()
            ->route('messages.show', $message)
            ->with('success', 'Message submitted! AI is generating your reply…');
    }

    public function show(Request $request, Message $message): View
    {
        abort_if($message->user_id !== $request->user()->id, 403);

        return view('messages.show', compact('message'));
    }

    public function destroy(Request $request, Message $message): RedirectResponse
    {
        abort_if($message->user_id !== $request->user()->id, 403);

        $message->delete();

        return redirect()->route('messages.index')->with('success', 'Message deleted.');
    }
}
