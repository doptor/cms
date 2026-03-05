<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>@yield('title', config('app.name')) — RyaanCMS</title>
    <meta name="description" content="@yield('description', 'AI-Powered Laravel CMS — Build complex applications with a simple prompt.')">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind CSS via CDN (no build step — shared hosting safe) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'ryaan': {
                            50:  '#f0f0ff',
                            100: '#e5e5ff',
                            500: '#6c63ff',
                            600: '#5a52e0',
                            700: '#4840c0',
                            900: '#1a1830',
                        },
                        'surface': '#111118',
                        'surface2': '#1a1a24',
                    },
                    fontFamily: {
                        'display': ['Syne', 'sans-serif'],
                        'mono':    ['DM Mono', 'monospace'],
                        'sans':    ['Instrument Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Alpine.js (no build step) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Global Styles --}}
    <style>
        * { scrollbar-width: thin; scrollbar-color: #222232 transparent; }
        *::-webkit-scrollbar { width: 5px; height: 5px; }
        *::-webkit-scrollbar-thumb { background: #222232; border-radius: 4px; }
        body { font-family: 'Instrument Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }
        .font-mono { font-family: 'DM Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>

<body class="bg-[#0a0a0f] text-gray-100 min-h-screen flex flex-col" x-data="{ sidebarOpen: true, theme: 'dark' }">

    {{-- HEADER --}}
    @include('layouts.partials.header')

    {{-- MAIN WRAPPER --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- SIDEBAR --}}
        @auth
            @include('layouts.partials.sidebar')
        @endauth

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-auto @auth bg-[#0a0a0f] @endauth">

            {{-- FLASH MESSAGES --}}
            @if (session('success'))
                <div class="mx-4 mt-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-lg text-sm" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 mt-4 px-4 py-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg text-sm" x-data x-init="setTimeout(() => $el.remove(), 4000)">
                    ❌ {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- STATUS BAR --}}
    <div class="h-7 bg-ryaan-500 flex items-center px-4 gap-4 text-xs text-white/80 font-mono flex-shrink-0">
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400"></span>
            Laravel 11.x
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            MySQL Connected
        </span>
        @auth
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->hasAnyAiKey() ? 'bg-emerald-400' : 'bg-yellow-400' }}"></span>
            AI: {{ auth()->user()->hasClaudeKey() ? 'Claude ✓' : '' }} {{ auth()->user()->hasDeepSeekKey() ? 'DeepSeek ✓' : '' }}
            @unless(auth()->user()->hasAnyAiKey())
                <a href="{{ route('settings.index') }}" class="text-yellow-300 underline">Add API Key</a>
            @endunless
        </span>
        @endauth
        <span class="ml-auto flex items-center gap-1.5">
            🌐 Shared Hosting Ready · RyaanCMS v1.0.0 · Free & Open Source
        </span>
    </div>

    {{-- TOAST NOTIFICATIONS --}}
    <div id="toastContainer" class="fixed top-16 right-4 z-50 flex flex-col gap-2"></div>

    {{-- GLOBAL JS --}}
    <script>
        // CSRF token for AJAX
        window.csrfToken = '{{ csrf_token() }}';

        // Global toast function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const colors = {
                success: 'bg-[#111118] border-emerald-500/40 text-emerald-400',
                error:   'bg-[#111118] border-red-500/40 text-red-400',
                info:    'bg-[#111118] border-ryaan-500/40 text-ryaan-400',
            };
            const icons = { success: '✅', error: '❌', info: 'ℹ️' };
            const el = document.createElement('div');
            el.className = `flex items-center gap-2 px-4 py-3 rounded-lg border ${colors[type]} text-sm shadow-xl transition-all duration-300 translate-x-full`;
            el.innerHTML = `<span>${icons[type]}</span><span>${message}</span>`;
            container.appendChild(el);
            setTimeout(() => el.classList.remove('translate-x-full'), 10);
            setTimeout(() => { el.classList.add('translate-x-full'); setTimeout(() => el.remove(), 300); }, 4000);
        }

        // Global fetch helper with CSRF
        async function apiPost(url, data = {}) {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(data),
            });
            return res.json();
        }

        async function apiDelete(url) {
            const res = await fetch(url, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' },
            });
            return res.json();
        }
    </script>

    @stack('scripts')
</body>
</html>
