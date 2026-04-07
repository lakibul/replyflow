<x-layouts.app title="Dashboard">
    <div class="py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-0.5">Welcome back, {{ auth()->user()->name }}</p>
            </div>
            <a href="{{ route('messages.create') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Message
            </a>
        </div>

        {{-- Quota card --}}
        <div class="mb-6 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 p-5 text-white shadow-md">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-indigo-200 mb-1">Monthly Usage —
                        <span class="uppercase font-semibold text-white">{{ $subscription->plan_name }}</span> Plan
                    </p>
                    <p class="text-3xl font-bold">{{ $subscription->used_requests }}
                        <span class="text-lg font-normal text-indigo-200">/ {{ $subscription->request_limit }}</span>
                    </p>
                    <p class="text-sm text-indigo-200 mt-1">
                        {{ $subscription->remainingRequests() }} requests remaining
                    </p>
                </div>
                @if($subscription->plan_name === 'free')
                    <span class="text-xs bg-white/20 px-3 py-1 rounded-full">Free Plan</span>
                @else
                    <span class="text-xs bg-yellow-400/90 text-yellow-900 font-semibold px-3 py-1 rounded-full">Pro</span>
                @endif
            </div>
            <div class="mt-4 w-full bg-indigo-800/50 rounded-full h-2">
                <div class="bg-white h-2 rounded-full transition-all"
                     style="width: {{ $subscription->request_limit > 0 ? min(100, round($subscription->used_requests / $subscription->request_limit * 100)) : 0 }}%">
                </div>
            </div>
        </div>

        {{-- Stats grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-400 mt-1">messages submitted</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Completed</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['completed'] }}</p>
                <p class="text-xs text-gray-400 mt-1">AI replies generated</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Pending</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
                <p class="text-xs text-gray-400 mt-1">in queue</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-medium text-red-500 uppercase tracking-wide">Failed</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['failed'] }}</p>
                <p class="text-xs text-gray-400 mt-1">processing errors</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Category breakdown --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Categories</h2>
                @if($stats['categories']->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-6">No categorized messages yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($stats['categories'] as $category => $count)
                            @php
                                $colors = ['Billing' => 'bg-purple-500', 'Technical' => 'bg-blue-500', 'General' => 'bg-teal-500'];
                                $barColor = $colors[$category] ?? 'bg-gray-400';
                                $pct = $stats['completed'] > 0 ? round($count / $stats['completed'] * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="font-medium text-gray-700">{{ $category }}</span>
                                    <span class="text-gray-500">{{ $count }} ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recent messages --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">Recent Messages</h2>
                    <a href="{{ route('messages.index') }}" class="text-xs text-indigo-600 hover:underline">View all</a>
                </div>
                @if($recentMessages->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <p class="text-sm text-gray-400">No messages yet.</p>
                        <a href="{{ route('messages.create') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:underline">
                            Submit your first message →
                        </a>
                    </div>
                @else
                    <ul class="divide-y divide-gray-50">
                        @foreach($recentMessages as $message)
                            <li>
                                <a href="{{ route('messages.show', $message) }}"
                                   class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-800 truncate">{{ $message->message_text }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $message->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if($message->category)
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $message->category }}</span>
                                        @endif
                                        <x-status-badge :status="$message->status"/>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
