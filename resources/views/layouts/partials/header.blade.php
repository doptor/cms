{{-- resources/views/layouts/partials/header.blade.php --}}
<header class="h-14 bg-[#111118] border-b border-white/5 flex items-center px-4 gap-3 flex-shrink-0 z-50">

    {{-- Logo + sidebar toggle --}}
    <div class="flex items-center gap-3" style="width:244px">
        @auth
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-300 transition-colors p-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        @endauth
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-gradient-to-br from-[#6c63ff] to-[#00d4aa] rounded-lg flex items-center justify-center text-sm shadow-lg shadow-[#6c63ff]/20">⚡</div>
            <span class="font-display font-bold text-lg text-white">Ryaan<span class="text-[#00d4aa]">CMS</span></span>
        </a>
    </div>

    {{-- Search --}}
    @auth
    <div class="flex-1 max-w-md flex items-center gap-2 bg-[#1a1a24] border border-white/8 rounded-xl px-3 py-2 hover:border-white/15 transition-colors">
        <span class="text-gray-600 text-sm">🔍</span>
        <input type="text" placeholder="Search projects, files, templates..." class="flex-1 bg-transparent text-sm text-gray-300 placeholder-gray-600 outline-none"/>
        <kbd class="text-xs text-gray-600 bg-[#111118] px-1.5 py-0.5 rounded font-mono">⌘K</kbd>
    </div>
    @endauth

    {{-- Right actions --}}
    <div class="flex items-center gap-2 ml-auto">
        @auth
        {{-- New Project --}}
        <a href="{{ route('projects.index') }}" class="hidden sm:flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-xs font-semibold hover:opacity-90 transition-all">
            ＋ New Project
        </a>

        {{-- Notifications --}}
        <button class="relative w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-300 hover:bg-white/5 transition-all">
            🔔
            <span class="absolute top-1 right-1 w-2 h-2 bg-[#6c63ff] rounded-full"></span>
        </button>

        {{-- Docs --}}
        <a href="#" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-white/10 text-xs text-gray-400 hover:border-white/20 hover:text-gray-300 transition-all">
            📖 Docs
        </a>

        {{-- Avatar dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="w-8 h-8 rounded-full bg-gradient-to-br from-[#6c63ff] to-purple-600 flex items-center justify-center text-xs font-bold text-white hover:ring-2 hover:ring-[#6c63ff]/40 transition-all">
                {{ substr(auth()->user()->name, 0, 1) }}
            </button>
            <div x-show="open" @click.away="open = false" x-cloak
                class="absolute right-0 top-10 w-48 bg-[#1a1a24] border border-white/10 rounded-xl shadow-2xl overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-white/5">
                    <div class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                </div>
                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition-all">⚙️ Settings</a>
                <a href="{{ route('marketplace.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition-all">🛒 Marketplace</a>
                @can('admin') <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 transition-all">🛡 Admin</a> @endcan
                <div class="border-t border-white/5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition-all text-left">⏻ Sign Out</button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-lg border border-white/10 text-sm text-gray-400 hover:border-white/20 transition-all">Login</a>
        <a href="{{ route('register') }}" class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all">Get Started Free</a>
        @endauth
    </div>
</header>
