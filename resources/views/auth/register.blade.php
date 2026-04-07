<x-layouts.guest title="Create account">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Create your account</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('name') border-red-400 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') border-red-400 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-400 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>

        <div class="rounded-lg bg-indigo-50 border border-indigo-100 px-4 py-3 text-xs text-indigo-700">
            Free plan includes <strong>20 AI requests/month</strong>. Upgrade any time for 500 requests.
        </div>

        <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            Create account
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
    </p>
</x-layouts.guest>
