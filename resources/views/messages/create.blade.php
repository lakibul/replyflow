<x-layouts.app title="New Message">
    <div class="py-6 max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Submit a Message</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Paste a customer message and select a tone. Our AI will generate a reply, summary, and category.
            </p>
        </div>

        @php $sub = auth()->user()->getOrCreateSubscription(); @endphp

        @if($sub->hasReachedLimit())
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-5">
                <p class="font-medium text-red-800 text-sm">Monthly limit reached</p>
                <p class="text-sm text-red-600 mt-1">
                    You've used all {{ $sub->request_limit }} requests on your
                    <strong class="capitalize">{{ $sub->plan_name }}</strong> plan this month.
                </p>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <form method="POST" action="{{ route('messages.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Customer Message <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message_text" rows="6" required minlength="10" maxlength="5000"
                              placeholder="Paste the customer's message here..."
                              class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none @error('message_text') border-red-400 @enderror">{{ old('message_text') }}</textarea>
                    @error('message_text')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">Minimum 10, maximum 5,000 characters.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Reply Tone</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach(['professional', 'friendly', 'formal', 'empathetic', 'assertive'] as $tone)
                            <label class="relative">
                                <input type="radio" name="tone" value="{{ $tone }}"
                                       {{ old('tone', 'professional') === $tone ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="cursor-pointer border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-center capitalize
                                            peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700
                                            hover:border-gray-300 transition-all">
                                    {{ $tone }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <div class="text-xs text-gray-500">
                        <span class="{{ $sub->remainingRequests() <= 3 ? 'text-red-500 font-medium' : '' }}">
                            {{ $sub->remainingRequests() }} requests left
                        </span>
                        this month
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('messages.index') }}"
                           class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                @if($sub->hasReachedLimit()) disabled @endif
                                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Generate AI Reply
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- How it works --}}
        <div class="mt-6 rounded-xl bg-gray-50 border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">How it works</p>
            <div class="grid sm:grid-cols-3 gap-3">
                @foreach([
                    ['💬', 'Reply', 'AI writes a polished reply in your chosen tone'],
                    ['📋', 'Summary', 'The customer issue is condensed to 1–2 sentences'],
                    ['🏷️', 'Category', 'Classified as Billing, Technical, or General'],
                ] as [$icon, $label, $desc])
                <div class="flex items-start gap-2">
                    <span class="text-lg">{{ $icon }}</span>
                    <div>
                        <p class="text-xs font-semibold text-gray-700">{{ $label }}</p>
                        <p class="text-xs text-gray-500">{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
