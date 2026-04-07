<x-layouts.app title="Messages">
    <div class="py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
                <p class="text-sm text-gray-500 mt-0.5">All submitted messages and their AI results</p>
            </div>
            <a href="{{ route('messages.create') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Message
            </a>
        </div>

        @if($messages->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-16 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <p class="text-gray-500 font-medium">No messages yet</p>
                <p class="text-sm text-gray-400 mt-1">Submit a customer message to get an AI-generated reply.</p>
                <a href="{{ route('messages.create') }}"
                   class="mt-4 inline-flex items-center gap-1 text-sm text-indigo-600 hover:underline font-medium">
                    Submit your first message →
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tone</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($messages as $message)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900 max-w-xs truncate">{{ $message->message_text }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs text-gray-600 capitalize">{{ $message->tone }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    @if($message->category)
                                        @php
                                            $catColors = [
                                                'Billing'   => 'bg-purple-100 text-purple-700',
                                                'Technical' => 'bg-blue-100 text-blue-700',
                                                'General'   => 'bg-teal-100 text-teal-700',
                                            ];
                                        @endphp
                                        <span class="inline-block text-xs font-medium px-2 py-0.5 rounded {{ $catColors[$message->category] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $message->category }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <x-status-badge :status="$message->status"/>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a href="{{ route('messages.show', $message) }}"
                                       class="text-xs text-indigo-600 hover:underline font-medium">View →</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($messages->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-layouts.app>
