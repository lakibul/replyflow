<x-layouts.app title="Message #{{ $message->id }}">
    <div class="py-6 max-w-3xl">
        {{-- Back + header --}}
        <div class="mb-6">
            <a href="{{ route('messages.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Messages
            </a>
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Message #{{ $message->id }}</h1>
                    <div class="flex items-center gap-3 mt-1">
                        <x-status-badge :status="$message->status"/>
                        <span class="text-xs text-gray-400">{{ $message->created_at->format('M j, Y \a\t g:i A') }}</span>
                        @if($message->category)
                            @php
                                $catColors = [
                                    'Billing'   => 'bg-purple-100 text-purple-700',
                                    'Technical' => 'bg-blue-100 text-blue-700',
                                    'General'   => 'bg-teal-100 text-teal-700',
                                ];
                            @endphp
                            <span class="text-xs font-medium px-2 py-0.5 rounded {{ $catColors[$message->category] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $message->category }}
                            </span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('messages.destroy', $message) }}"
                      onsubmit="return confirm('Delete this message?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="text-xs text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 px-3 py-1.5 rounded-lg transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Pending / Processing state --}}
        @if(in_array($message->status, ['pending', 'processing']))
            <div class="mb-6 rounded-xl bg-blue-50 border border-blue-200 p-5 flex items-center gap-4">
                <div class="w-8 h-8 rounded-full border-2 border-blue-400 border-t-transparent animate-spin shrink-0"></div>
                <div>
                    <p class="text-sm font-medium text-blue-800">AI is processing your message…</p>
                    <p class="text-xs text-blue-600 mt-0.5">
                        This usually takes a few seconds. Refresh to see the result.
                    </p>
                </div>
                <a href="{{ route('messages.show', $message) }}"
                   class="ml-auto text-xs text-blue-600 hover:underline font-medium shrink-0">
                    Refresh ↻
                </a>
            </div>
        @endif

        {{-- Failed state --}}
        @if($message->status === 'failed')
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-5">
                <p class="text-sm font-medium text-red-800">Processing failed</p>
                @if($message->error_message)
                    <p class="text-xs text-red-600 mt-1 font-mono">{{ $message->error_message }}</p>
                @endif
            </div>
        @endif

        {{-- Original message --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-700">Customer Message</h2>
                <span class="text-xs text-gray-400 capitalize bg-gray-100 px-2 py-0.5 rounded">
                    Tone: {{ $message->tone }}
                </span>
            </div>
            <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $message->message_text }}</p>
        </div>

        {{-- AI Results --}}
        @if($message->isCompleted())
            {{-- AI Reply --}}
            <div class="bg-white rounded-xl border border-indigo-100 shadow-sm p-6 mb-4 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500 rounded-l-xl"></div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">💬</span>
                        <h2 class="text-sm font-semibold text-gray-800">AI Reply</h2>
                    </div>
                    <button onclick="copyText(this, 'reply-text')"
                            class="text-xs text-gray-500 hover:text-indigo-600 border border-gray-200 hover:border-indigo-300 px-2.5 py-1 rounded transition-colors">
                        Copy
                    </button>
                </div>
                <p id="reply-text" class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $message->ai_reply }}</p>
            </div>

            {{-- Summary --}}
            <div class="bg-white rounded-xl border border-green-100 shadow-sm p-6 mb-4 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-green-500 rounded-l-xl"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg">📋</span>
                    <h2 class="text-sm font-semibold text-gray-800">Summary</h2>
                </div>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $message->summary }}</p>
            </div>

            {{-- Category --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-amber-400 rounded-l-xl"></div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg">🏷️</span>
                    <h2 class="text-sm font-semibold text-gray-800">Category</h2>
                </div>
                @php
                    $catBig = [
                        'Billing'   => ['bg-purple-100 text-purple-800', 'Relates to payments, invoices, or account charges.'],
                        'Technical' => ['bg-blue-100 text-blue-800',   'A technical issue or product/service problem.'],
                        'General'   => ['bg-teal-100 text-teal-800',   'General inquiry or feedback.'],
                    ];
                    [$catClass, $catDesc] = $catBig[$message->category] ?? ['bg-gray-100 text-gray-700', ''];
                @endphp
                <span class="inline-block text-sm font-semibold px-4 py-1.5 rounded-lg {{ $catClass }}">
                    {{ $message->category }}
                </span>
                <p class="mt-2 text-xs text-gray-500">{{ $catDesc }}</p>
            </div>
        @endif

        {{-- Footer actions --}}
        <div class="mt-6 flex gap-3">
            <a href="{{ route('messages.create') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                Submit another message
            </a>
            <a href="{{ route('messages.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                All messages
            </a>
        </div>
    </div>
</x-layouts.app>

<script>
function copyText(btn, id) {
    const text = document.getElementById(id).textContent;
    navigator.clipboard.writeText(text.trim()).then(() => {
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = 'Copy', 2000);
    });
}
</script>
