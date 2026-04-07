<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ReplyFlow' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center mb-4">
            <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>
        <h1 class="text-center text-3xl font-bold text-white mb-1">ReplyFlow</h1>
        <p class="text-center text-sm text-indigo-300">AI-powered customer support replies</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-2xl rounded-2xl sm:px-10">
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
