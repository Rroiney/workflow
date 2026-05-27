@php
use App\Models\Tenant;

$user = auth('tenant')->user();

/* ---------------- USER AVATAR ---------------- */
$avatar = $user->profile_photo_path
? asset('storage/' . $user->profile_photo_path)
: null;

/* ---------------- TENANT ---------------- */
$tenantSlug = request()->route('tenant');

/* tenant record from workflow_system DB */
$tenantData = Tenant::where('slug', $tenantSlug)->first();

/* ---------------- NAV LINK HELPER ---------------- */
if (!function_exists('navLink')) {
function navLink($routeName, $label, $tenantSlug, $iconPath) {

    $base = str_contains($routeName, '.')
    ? explode('.', $routeName)[0]
    : $routeName;

    $active = request()->routeIs($base . '.*') || request()->routeIs($routeName)
    ? 'bg-indigo-50 text-indigo-600 font-medium'
    : 'hover:bg-slate-100';

    return '
    <a href="' . route($routeName, ['tenant' => $tenantSlug]) . '"
        class="flex items-center gap-3 px-4 py-2 rounded-lg transition ' . $active . '">

        <img src="' . asset($iconPath) . '"
            class="w-5 h-5 opacity-70">

        <span>' . $label . '</span>
    </a>';
}
}

@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>
        @hasSection('title')
        @yield('title') | WorkFlow
        @else
        WorkFlow
        @endif
    </title>

    <link rel="icon" type="image/png"
        href="{{ asset('assets/branding/workflow-logo.png') }}">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
        content="ie=edge">
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


    {{-- Laravel 12 + Tailwind v4 --}}
    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

    <!-- Prevent Alpine flash -->
    <style>
        [x-cloak] {
            display: none !important;
        }

        body[data-loaded="false"] main,
        body[data-loaded="false"] header,
        body[data-loaded="false"] aside,
        body[data-loaded="false"] footer {
            visibility: hidden;
        }

        #page-loader {
            transition: opacity 0.4s ease;
        }
    </style>

    </style>
</head>

<body class="bg-slate-100 text-slate-700"
    data-loaded="false">

    <!-- ================= PAGE LOADER ================= -->
    <div id="page-loader"
        class="fixed inset-0 z-[9999] bg-white flex items-center justify-center">

        <div class="flex flex-col items-center gap-3">
            <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600
                    rounded-full animate-spin"></div>
            <p class="text-sm text-slate-500">Loading...</p>
        </div>
    </div>

    <!-- ================= SUCCESS ALERT ================= -->
    @if(session('success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
        class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999]">

        <div class="flex items-start gap-3
                bg-white border border-emerald-200
                rounded-xl shadow-xl
                px-5 py-3 min-w-[280px]">

            <!-- Icon -->
            <div class="w-9 h-9 rounded-full bg-emerald-100
                    flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-emerald-600"
                    fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <!-- Message -->
            <div class="text-sm text-slate-700 leading-snug pt-1">
                {{ session('success') }}
            </div>

            <!-- Close -->
            <!-- <button
                @click="show = false"
                class="ml-2 text-slate-400 hover:text-slate-600 transition">
                ✕
            </button> -->
        </div>
    </div>
    @endif


    <!-- ================= SIDEBAR ================= -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 flex flex-col z-50">

        <!-- LOGO -->
        <div class="px-4 py-4 flex items-center justify-center min-h-[88px]">
            @if($tenantData && $tenantData->company_logo_path)
            <img src="{{ asset('storage/' . $tenantData->company_logo_path) }}"
                class="max-h-16 w-full max-w-[220px] object-contain"
                alt="Company Logo">
            @else
            <span class="text-2xl font-bold text-indigo-600">
                {{ $tenantData->name ?? 'WorkFlow' }}
            </span>
            @endif
        </div>

        <!-- NAVIGATION -->
        <nav class="flex-1 px-4 space-y-1 overflow-y-auto">

            {!! navLink('home', 'Home', $tenantSlug, 'icons/sidebar/home.png') !!}

            {!! navLink(
            'tasks.index',
            auth('tenant')->user()->isEmployee() ? 'My Tasks' : 'Tasks',
            $tenantSlug,
            'icons/sidebar/tasks.png'
            ) !!}

            {!! navLink('leaves.index', 'Leaves', $tenantSlug, 'icons/sidebar/leaves.png') !!}

            {!! navLink('documents.index', 'Documents', $tenantSlug, 'icons/sidebar/documents.png') !!}

            @if(auth('tenant')->user()->isAdmin())
            {!! navLink('teams.index', 'Teams', $tenantSlug, 'icons/sidebar/teams.png') !!}
            @endif

            <div class="pt-4">
                <p class="px-4 pb-2 text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                    Coming Soon
                </p>

                <a href="{{ route('upcoming.index', ['tenant' => $tenantSlug]) }}"
                    class="flex items-center justify-between gap-3 px-4 py-2 rounded-lg transition {{ request()->routeIs('upcoming.index') ? 'bg-indigo-50 text-indigo-600 font-medium' : 'hover:bg-slate-100' }}">
                    <span class="text-sm">Upcoming Features</span>
                    <span class="text-sm leading-none" aria-label="Work in progress" title="Work in progress">🛠</span>
                </a>
            </div>

        </nav>

    </aside>

    <!-- ================= MAIN WRAPPER ================= -->
    <div class="ml-64 min-h-screen flex flex-col">

        <!-- HEADER -->
        <header class="sticky top-0 z-40 bg-white px-8 py-4
               flex justify-end items-center
               border-b border-slate-200">

            @php
            $user = auth('tenant')->user();
            $normalizeIp = function ($ip) {
            return preg_replace('/^::ffff:/', '', trim((string) $ip));
            };
            $officeIPs = collect(config('workflow.office_ips', []))
            ->map($normalizeIp)
            ->filter()
            ->values()
            ->all();
            $lastLoginIp = $normalizeIp($user->last_login_ip);
            $today = now()->toDateString();

            if (!$user->last_login_at || $user->last_login_at->toDateString() !== $today) {
            $status = 'Unavailable';
            $statusClass = 'bg-slate-100 text-slate-500';
            } elseif (in_array($lastLoginIp, $officeIPs, true)) {
            $status = 'WFO';
            $statusClass = 'bg-emerald-100 text-emerald-700';
            } else {
            $status = 'WFH';
            $statusClass = 'bg-indigo-100 text-indigo-700';
            }
            @endphp

            <!-- RIGHT: USER DROPDOWN (UNCHANGED) -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 text-sm">
                    <span class="font-medium text-slate-700">Status</span>

                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium {{ $statusClass }}">
                        {{ $status }}
                    </span>

                    <div class="relative group flex items-center ml-1">
                        <img
                            src="{{ asset('icons/information.png') }}"
                            alt="Info"
                            class="w-4 h-4 opacity-80 hover:opacity-100 cursor-pointer">

                        <div
                            class="absolute left-1/2 -translate-x-1/2 top-6
                               whitespace-nowrap
                               bg-slate-800 text-white
                               text-xs px-2 py-1 rounded
                               opacity-0 group-hover:opacity-100
                               transition pointer-events-none">
                            Status is based on login activity
                        </div>
                    </div>
                </div>

                <span class="hidden sm:block h-8 w-px bg-slate-200"></span>

                <div
                    x-data="{
                        open: false,
                        preference: 'system',
                        effectiveTheme: 'light',
                        syncTheme() {
                            this.preference = window.workflowTheme?.getPreference?.() ?? 'system';
                            this.effectiveTheme = window.workflowTheme?.getEffectiveTheme?.() ?? 'light';
                        },
                        setTheme(preference) {
                            window.workflowTheme?.setPreference?.(preference);
                            this.syncTheme();
                            this.open = false;
                        }
                    }"
                    x-init="
                        syncTheme();
                        window.addEventListener('workflow-theme-changed', () => syncTheme());
                    "
                    class="relative">
                    <button
                        type="button"
                        @click="open = !open"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50"
                        :title="preference === 'system' ? 'System theme' : (preference === 'dark' ? 'Dark theme' : 'Light theme')"
                        aria-label="Theme switcher">
                        <svg x-show="preference === 'light'" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2.5M12 19.5V22M4.93 4.93l1.77 1.77M17.3 17.3l1.77 1.77M2 12h2.5M19.5 12H22M4.93 19.07l1.77-1.77M17.3 6.7l1.77-1.77"></path>
                        </svg>

                        <svg x-show="preference === 'dark'" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 14.7A9 9 0 0 1 9.3 3a1 1 0 0 0-1.26 1.26A7 7 0 1 0 19.74 16a1 1 0 0 0 1.26-1.3Z"></path>
                        </svg>

                        <div x-show="preference === 'system'" x-cloak class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="4" width="18" height="12" rx="2"></rect>
                                <path d="M8 20h8M12 16v4"></path>
                            </svg>
                            <span class="absolute -right-1 -top-1 inline-flex h-2.5 w-2.5 rounded-full"
                                :class="effectiveTheme === 'dark' ? 'bg-indigo-400' : 'bg-amber-400'"></span>
                        </div>
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        @click.outside="open = false"
                        class="absolute right-0 mt-2 w-44 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl z-50">
                        <div class="mb-2 px-2 pt-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                            Theme
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <button
                                type="button"
                                @click="setTheme('light')"
                                class="flex flex-col items-center gap-2 rounded-xl px-3 py-3 transition hover:bg-slate-50"
                                :class="preference === 'light' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'text-slate-700'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="4"></circle>
                                    <path d="M12 2v2.5M12 19.5V22M4.93 4.93l1.77 1.77M17.3 17.3l1.77 1.77M2 12h2.5M19.5 12H22M4.93 19.07l1.77-1.77M17.3 6.7l1.77-1.77"></path>
                                </svg>
                                <span class="text-[11px] font-medium">Light</span>
                            </button>

                            <button
                                type="button"
                                @click="setTheme('dark')"
                                class="flex flex-col items-center gap-2 rounded-xl px-3 py-3 transition hover:bg-slate-50"
                                :class="preference === 'dark' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'text-slate-700'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21 14.7A9 9 0 0 1 9.3 3a1 1 0 0 0-1.26 1.26A7 7 0 1 0 19.74 16a1 1 0 0 0 1.26-1.3Z"></path>
                                </svg>
                                <span class="text-[11px] font-medium">Dark</span>
                            </button>

                            <button
                                type="button"
                                @click="setTheme('system')"
                                class="flex flex-col items-center gap-2 rounded-xl px-3 py-3 transition hover:bg-slate-50"
                                :class="preference === 'system' ? 'bg-indigo-50 text-indigo-600 ring-1 ring-indigo-200' : 'text-slate-700'">
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <rect x="3" y="4" width="18" height="12" rx="2"></rect>
                                        <path d="M8 20h8M12 16v4"></path>
                                    </svg>
                                    <span class="absolute -right-1 -top-1 inline-flex h-2.5 w-2.5 rounded-full"
                                        :class="effectiveTheme === 'dark' ? 'bg-indigo-400' : 'bg-amber-400'"></span>
                                </div>
                                <span class="text-[11px] font-medium">Auto</span>
                            </button>
                        </div>

                        <p class="mt-2 px-2 pb-1 text-center text-[11px] text-slate-400" x-show="preference === 'system'" x-cloak>
                            Using <span x-text="effectiveTheme"></span> from your device
                        </p>
                    </div>
                </div>

                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        class="flex items-center gap-3 text-sm font-medium
                       text-slate-700 hover:text-indigo-600 cursor-pointer">

                        {{-- AVATAR --}}
                        @if($avatar)
                        <img src="{{ $avatar }}"
                            class="w-8 h-8 rounded-full object-cover">
                        @else
                        <div class="w-8 h-8 rounded-full bg-indigo-100
                                flex items-center justify-center
                                text-indigo-600 font-semibold text-xs">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @endif

                        <span>Hello, <span class="font-semibold text-slate-900">{{ $user->name }}</span></span>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                        class="absolute right-0 mt-2 w-40 bg-white
                        rounded-xl shadow-lg border border-slate-100 z-50">

                        <form method="POST"
                            action="{{ url('/org/' . $tenantSlug . '/logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm
                                   text-red-600 hover:bg-red-50 rounded-xl">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </header>


        <!-- PAGE CONTENT -->
        <main class="flex-1 p-8 space-y-8">
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

    </div>

    <script>
        (function() {
            const body = document.body;
            const loader = document.getElementById('page-loader');

            // Initial page load
            window.addEventListener('load', () => {
                setTimeout(() => {
                    body.dataset.loaded = "true";
                    loader.style.opacity = "0";

                    setTimeout(() => {
                        loader.style.display = "none";
                    }, 400);
                }, 600); // ensures loader is actually visible
            });

            // Show loader on navigation
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript')) return;
                if (link.hasAttribute('download') || link.dataset.skipLoader === 'true') return;

                loader.style.display = "flex";
                loader.style.opacity = "1";
                body.dataset.loaded = "false";
            });
        })();
    </script>


</body>

</html>
