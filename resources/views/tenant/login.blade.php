<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | {{ $tenant }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png"
        href="{{ asset('assets/branding/workflow-logo.png') }}">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
        content="ie=edge">
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
            <form method="POST" action="" class="space-y-4">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Email
                    </label>
                    <input type="email"
                        name="email"
                        required
                        placeholder="you@domain.com"
                        class="w-full text-sm
                        bg-transparent
                        border-0
                        cursor-text
                        focus:outline-none
                        focus:ring-0">
                </div>

                {{-- PASSWORD --}}
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        Password
                    </label>
                    <input type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full text-sm
              bg-transparent
              border-0
              cursor-text
              focus:outline-none
              focus:ring-0">


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