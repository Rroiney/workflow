<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Welcome')</title>
    @vite([
    'resources/js/app.js',
    ])
    <link rel="icon" type="image/png"
        href="{{ asset('assets/branding/workflow-logo.png') }}">
</head>

<body class="bg-slate-50 text-slate-800">

    <main class="max-w-7xl mx-auto px-6 py-10">
        @yield('content')
    </main>

    <footer class="py-4">
        <p class="text-xs text-slate-400 text-center">
            © {{ date('Y') }} WorkFlow Inc. All Rights Reserved.
        </p>
        <p class="text-[11px] text-slate-400/80 text-center tracking-tight
              hover:text-slate-500 transition mt-1">
            Created with ❤️ by Akash
        </p>
    </footer>

</body>

</html>