<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ReplyFlow' }} — AI Support Assistant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
<div class="min-h-full flex">

    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-slate-900">
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <span class="text-white font-semibold text-lg">ReplyFlow</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-4 py-6 space-y-1">
            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </x-nav-link>

            <x-nav-link href="{{ route('messages.index') }}" :active="request()->routeIs('messages.*')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Messages
            </x-nav-link>

            <x-nav-link href="{{ route('messages.create') }}" :active="false">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                New Message
            </x-nav-link>
        </nav>

        {{-- User / Logout --}}
        <div class="px-4 py-4 border-t border-slate-700">
            @php $sub = auth()->user()->getOrCreateSubscription(); @endphp
            <div class="mb-3 px-3 py-2 rounded-lg bg-slate-800">
                <p class="text-xs text-slate-400 mb-1">Monthly Usage</p>
                <div class="w-full bg-slate-700 rounded-full h-1.5 mb-1">
                    <div class="bg-indigo-500 h-1.5 rounded-full"
                         style="width: {{ $sub->request_limit > 0 ? min(100, round($sub->used_requests / $sub->request_limit * 100)) : 0 }}%">
                    </div>
                </div>
                <p class="text-xs text-slate-300">{{ $sub->used_requests }} / {{ $sub->request_limit }}
                    <span class="ml-1 capitalize text-indigo-400 font-medium">{{ $sub->plan_name }}</span>
                </p>
            </div>

            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out"
                            class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 lg:pl-64 flex flex-col">
        {{-- Top bar (mobile) --}}
        <header class="lg:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <span class="font-semibold text-gray-900">ReplyFlow</span>
            <div class="flex items-center gap-4">
                <a href="{{ route('messages.create') }}"
                   class="text-sm text-indigo-600 font-medium">+ New</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-gray-500">Sign out</button>
                </form>
            </div>
        </header>

        {{-- Mobile Nav --}}
        <nav class="lg:hidden bg-white border-b border-gray-100 px-4 py-2 flex gap-4">
            <a href="{{ route('dashboard') }}"
               class="text-sm {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                Dashboard
            </a>
            <a href="{{ route('messages.index') }}"
               class="text-sm {{ request()->routeIs('messages.*') ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                Messages
            </a>
        </nav>

        {{-- Flash messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 px-6 pb-10">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
