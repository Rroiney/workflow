<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Welcome')</title>
    <meta name="color-scheme" content="light dark">
    <script>
        (function() {
            const storageKey = 'workflow-theme-preference';
            const storedPreference = localStorage.getItem(storageKey);
            const preference = ['light', 'dark', 'system'].includes(storedPreference) ? storedPreference : 'system';
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const effectiveTheme = preference === 'system' ? systemTheme : preference;
            document.documentElement.classList.toggle('dark', effectiveTheme === 'dark');
            document.documentElement.dataset.theme = effectiveTheme;
            document.documentElement.style.colorScheme = effectiveTheme;
        })();
    </script>
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
