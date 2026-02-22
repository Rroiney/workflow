<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | {{ $tenant }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png"
        href="{{ asset('assets/branding/workflow-logo.png') }}">
    <meta http-equiv="X-UA-Compatible"
        content="ie=edge">

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-slate-100 flex items-center justify-center">

    <div class="w-full max-w-md">

        {{-- CARD --}}
        <div class="bg-white rounded-xl shadow-sm p-6">

            {{-- HEADING --}}
            <h2 class="text-xl font-semibold text-slate-800 mb-1 text-center">
                Login
            </h2>
            <p class="text-sm text-slate-500 text-center mb-6">
                Sign in to <span class="font-medium text-indigo-600">{{ $tenant }}</span> dashboard
            </p>

            {{-- ERROR --}}
            @if ($errors->any())
            <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                {{ $errors->first() }}
            </div>
            @endif

            {{-- FORM --}}
            <form method="POST" action="{{ url()->current() }}" class="space-y-4">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-600 mb-1">
                        Email
                    </label>
                    <input id="email"
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        value="{{ old('email') }}"
                        placeholder="you@domain.com"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm
                        focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                {{-- PASSWORD --}}
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-medium text-slate-600 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <input id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pr-16 text-sm
              focus:border-indigo-500 focus:ring-indigo-500">
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-indigo-600"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'">
                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.584 10.587a2 2 0 0 0 2.828 2.828" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.77 9.77 0 0 1 12 4.5c6 0 9.75 7.5 9.75 7.5a16.9 16.9 0 0 1-4.23 4.94" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.61 6.61A16.93 16.93 0 0 0 2.25 12s3.75 7.5 9.75 7.5a9.7 9.7 0 0 0 4.04-.85" />
                            </svg>
                        </button>
                    </div>

                </div>
                {{-- REMEMBER ME --}}
                <div class="flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center gap-2.5 text-sm text-slate-600 cursor-pointer select-none leading-none">
                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                            value="1"
                            @checked(old('remember'))
                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 shrink-0">
                        <span class="pt-px">Remember me</span>
                    </label>
                </div>
                {{-- SUBMIT --}}
                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white py-2 rounded-lg
                                   hover:bg-indigo-700 transition">
                        Login
                    </button>
                </div>
            </form>

        </div>

        <footer class="py-4">
            <p class="text-xs text-slate-400 text-center">
                © {{ date('Y') }} WorkFlow Inc. All Rights Reserved.
            </p>
            <p class="text-[11px] text-slate-400/80 text-center tracking-tight
              hover:text-slate-500 transition mt-1">
                Created with ❤️ by Akash
            </p>
        </footer>


    </div>

</body>

</html>

