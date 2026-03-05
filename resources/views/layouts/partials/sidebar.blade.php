{{-- resources/views/layouts/partials/sidebar.blade.php --}}
<aside class="w-64 bg-[#111118] border-r border-white/5 flex flex-col flex-shrink-0 overflow-y-auto"
    :class="sidebarOpen ? 'w-64' : 'w-16'" style="transition: width 0.2s ease">

    {{-- NAV SECTIONS --}}
    <nav class="flex-1 py-4">

        {{-- Workspace --}}
        <div class="px-3 mb-1">
            <div class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-gray-600" x-show="sidebarOpen">Workspace</div>
        </div>

        @php
        $navItems = [
            ['route'=>'dashboard',         'icon'=>'🏠', 'label'=>'Dashboard'],
            ['route'=>'projects.index',    'icon'=>'📁', 'label'=>'Projects'],
            ['route'=>'marketplace.index', 'icon'=>'🛒', 'label'=>'Marketplace'],
        ];
        $buildItems = [
            ['route'=>'#', 'icon'=>'🎨', 'label'=>'Page Builder'],
            ['route'=>'#', 'icon'=>'🧩', 'label'=>'Components'],
        ];
        $systemItems = [
            ['route'=>'dashboard.seo',    'icon'=>'🔍', 'label'=>'SEO Manager'],
            ['route'=>'#',                'icon'=>'📈', 'label'=>'Analytics'],
            ['route'=>'#',                'icon'=>'👥', 'label'=>'Users'],
            ['route'=>'settings.index',   'icon'=>'🔑', 'label'=>'API Keys'],
            ['route'=>'#',                'icon'=>'⚙️',  'label'=>'Settings'],
        ];
        @endphp

        @foreach($navItems as $item)
        <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
            class="flex items-center gap-3 mx-3 px-3 py-2.5 rounded-xl text-sm transition-all
                {{ request()->routeIs(str_replace('.index','',$item['route']).'*') ? 'bg-[#6c63ff]/15 text-[#6c63ff]' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
            <span class="text-base flex-shrink-0">{{ $item['icon'] }}</span>
            <span x-show="sidebarOpen" class="truncate">{{ $item['label'] }}</span>
        </a>
        @endforeach

        <div class="px-3 mt-4 mb-1">
            <div class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-gray-600" x-show="sidebarOpen">Build</div>
        </div>

        @foreach($buildItems as $item)
        <a href="#" class="flex items-center gap-3 mx-3 px-3 py-2.5 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-white/5 transition-all">
            <span class="text-base flex-shrink-0">{{ $item['icon'] }}</span>
            <span x-show="sidebarOpen" class="truncate">{{ $item['label'] }}</span>
        </a>
        @endforeach

        <div class="px-3 mt-4 mb-1">
            <div class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-gray-600" x-show="sidebarOpen">System</div>
        </div>

        @foreach($systemItems as $item)
        <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
            class="flex items-center gap-3 mx-3 px-3 py-2.5 rounded-xl text-sm
                {{ request()->routeIs(str_replace('.index','',$item['route']).'*') ? 'bg-[#6c63ff]/15 text-[#6c63ff]' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition-all">
            <span class="text-base flex-shrink-0">{{ $item['icon'] }}</span>
            <span x-show="sidebarOpen" class="truncate">{{ $item['label'] }}</span>
        </a>
        @endforeach

        @can('admin')
        <div class="mx-3 mt-4 pt-4 border-t border-white/5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-500/10 transition-all">
                <span class="text-base">🛡</span>
                <span x-show="sidebarOpen">Admin Panel</span>
            </a>
        </div>
        @endcan
    </nav>

    {{-- API STATUS --}}
    <div class="m-3 p-3 bg-[#1a1a24] border border-white/5 rounded-xl" x-show="sidebarOpen">
        <div class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">AI Engines</div>
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <div class="w-1.5 h-1.5 rounded-full {{ auth()->user()->hasClaudeKey() ? 'bg-emerald-400 shadow-sm shadow-emerald-400/60 animate-pulse' : 'bg-gray-600' }}"></div>
                    Claude
                </div>
                <span class="text-xs {{ auth()->user()->hasClaudeKey() ? 'text-emerald-400' : 'text-gray-600' }}">
                    {{ auth()->user()->hasClaudeKey() ? '●' : '○' }}
                </span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <div class="w-1.5 h-1.5 rounded-full {{ auth()->user()->hasDeepSeekKey() ? 'bg-emerald-400 shadow-sm shadow-emerald-400/60 animate-pulse' : 'bg-gray-600' }}"></div>
                    DeepSeek
                </div>
                <span class="text-xs {{ auth()->user()->hasDeepSeekKey() ? 'text-emerald-400' : 'text-gray-600' }}">
                    {{ auth()->user()->hasDeepSeekKey() ? '●' : '○' }}
                </span>
            </div>
        </div>
        @unless(auth()->user()->hasAnyAiKey())
        <a href="{{ route('settings.index') }}" class="mt-2 block text-center text-xs text-[#6c63ff] hover:underline">Add API Keys →</a>
        @endunless
    </div>

    {{-- USER --}}
    <div class="m-3 flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 cursor-pointer transition-all" x-show="sidebarOpen">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#6c63ff] to-purple-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-sm text-white truncate">{{ auth()->user()->name }}</div>
            <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-gray-600 hover:text-gray-400 transition-colors text-sm" title="Logout">⏻</button>
        </form>
    </div>
</aside>
