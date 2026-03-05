@extends('layouts.app')
@section('title', 'Analytics')

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="analytics()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display font-bold text-2xl text-white">📈 Analytics</h1>
            <p class="text-gray-500 text-sm mt-1">Your platform insights — no third-party tracking</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex bg-[#111118] border border-white/8 rounded-xl p-1 gap-0.5">
                @foreach(['7d','30d','90d','1y'] as $range)
                <button @click="dateRange = '{{ $range }}'"
                    :class="dateRange === '{{ $range }}' ? 'bg-[#6c63ff] text-white' : 'text-gray-500 hover:text-gray-300'"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">{{ $range }}</button>
                @endforeach
            </div>
            <button class="flex items-center gap-2 px-4 py-2 rounded-xl border border-white/10 text-xs text-gray-400 hover:border-white/20 transition-all">
                📥 Export CSV
            </button>
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Page Views</span>
                <span class="text-emerald-400 text-xs font-semibold">+24%</span>
            </div>
            <div class="text-3xl font-bold text-white font-display mb-1">48,291</div>
            <div class="text-xs text-gray-600">vs 38,941 last period</div>
            <div class="mt-3 h-10" id="sparkline1">
                <canvas id="spark1" height="40"></canvas>
            </div>
        </div>
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Unique Visitors</span>
                <span class="text-emerald-400 text-xs font-semibold">+18%</span>
            </div>
            <div class="text-3xl font-bold text-white font-display mb-1">12,847</div>
            <div class="text-xs text-gray-600">vs 10,889 last period</div>
            <div class="mt-3 h-10"><canvas id="spark2" height="40"></canvas></div>
        </div>
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider">AI Requests</span>
                <span class="text-emerald-400 text-xs font-semibold">+41%</span>
            </div>
            <div class="text-3xl font-bold text-white font-display mb-1">3,204</div>
            <div class="text-xs text-gray-600">vs 2,272 last period</div>
            <div class="mt-3 h-10"><canvas id="spark3" height="40"></canvas></div>
        </div>
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Deployments</span>
                <span class="text-amber-400 text-xs font-semibold">+8%</span>
            </div>
            <div class="text-3xl font-bold text-white font-display mb-1">89</div>
            <div class="text-xs text-gray-600">vs 82 last period</div>
            <div class="mt-3 h-10"><canvas id="spark4" height="40"></canvas></div>
        </div>
    </div>

    {{-- MAIN CHART + BREAKDOWN --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- TRAFFIC CHART --}}
        <div class="lg:col-span-2 bg-[#111118] border border-white/5 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-white">Traffic Overview</h2>
                <div class="flex items-center gap-4 text-xs">
                    <span class="flex items-center gap-1.5 text-gray-400"><span class="w-2.5 h-2.5 rounded-full bg-[#6c63ff]"></span>Page Views</span>
                    <span class="flex items-center gap-1.5 text-gray-400"><span class="w-2.5 h-2.5 rounded-full bg-[#00d4aa]"></span>Visitors</span>
                </div>
            </div>
            <div class="h-56 flex items-end gap-1.5">
                @php
                $views   = [820,940,760,1100,980,1240,1080,1350,1190,1420,1300,1580,1240,1700];
                $visits  = [410,480,380,560,490,620,540,670,590,710,650,790,620,850];
                $maxVal  = max($views);
                @endphp
                @foreach($views as $i => $v)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full flex flex-col gap-0.5">
                        <div class="w-full bg-[#6c63ff] rounded-t-sm hover:bg-[#7c73ff] transition-colors cursor-pointer group relative"
                            style="height: {{ round(($v/$maxVal)*180) }}px">
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[#1a1a24] border border-white/10 rounded-lg px-2 py-1 text-xs text-white whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                {{ number_format($v) }} views
                            </div>
                        </div>
                        <div class="w-full bg-[#00d4aa]/60 rounded-b-sm hover:bg-[#00d4aa] transition-colors cursor-pointer"
                            style="height: {{ round(($visits[$i]/$maxVal)*180) }}px"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-3 text-xs text-gray-600 font-mono">
                @foreach(['Day 1','','Day 3','','Day 5','','Day 7','','Day 9','','Day 11','','Day 13',''] as $d)
                <span class="flex-1 text-center">{{ $d }}</span>
                @endforeach
            </div>
        </div>

        {{-- TOP PAGES --}}
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
            <h2 class="font-semibold text-white mb-4">🔝 Top Pages</h2>
            <div class="space-y-3">
                @foreach([
                    ['/'           ,'Home',        '8,291', 72],
                    ['/pricing'    ,'Pricing',     '4,102', 45],
                    ['/features'   ,'Features',    '3,847', 42],
                    ['/blog'       ,'Blog',        '2,901', 32],
                    ['/contact'    ,'Contact',     '1,840', 21],
                    ['/about'      ,'About',       '1,203', 14],
                ] as [$url,$label,$views,$pct])
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-300 font-mono">{{ $url }}</span>
                        <span class="text-xs text-gray-500">{{ $views }}</span>
                    </div>
                    <div class="h-1.5 bg-[#1a1a24] rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-[#6c63ff] to-purple-500 rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SECOND ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- GEO --}}
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
            <h2 class="font-semibold text-white mb-4">🌍 Top Countries</h2>
            <div class="space-y-3">
                @foreach([
                    ['🇺🇸','United States','4,201','33%'],
                    ['🇬🇧','United Kingdom','1,840','14%'],
                    ['🇩🇪','Germany',       '1,203','9%'],
                    ['🇮🇳','India',         '1,102','8%'],
                    ['🇦🇺','Australia',     '891', '7%'],
                    ['🇨🇦','Canada',        '740', '6%'],
                ] as [$flag,$country,$visitors,$pct])
                <div class="flex items-center gap-3">
                    <span class="text-lg">{{ $flag }}</span>
                    <div class="flex-1">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-300">{{ $country }}</span>
                            <span class="text-gray-500">{{ $visitors }}</span>
                        </div>
                        <div class="h-1 bg-[#1a1a24] rounded-full">
                            <div class="h-full bg-[#6c63ff]/60 rounded-full" style="width:{{ $pct }}"></div>
                        </div>
                    </div>
                    <span class="text-xs text-gray-600 w-8 text-right">{{ $pct }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- DEVICES --}}
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
            <h2 class="font-semibold text-white mb-4">📱 Devices</h2>
            <div class="flex items-center justify-center mb-6">
                {{-- Donut chart (CSS only) --}}
                <div class="relative w-32 h-32">
                    <svg viewBox="0 0 36 36" class="w-32 h-32 -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#1a1a24" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#6c63ff" stroke-width="3"
                            stroke-dasharray="52 48" stroke-dashoffset="0"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#00d4aa" stroke-width="3"
                            stroke-dasharray="35 65" stroke-dashoffset="-52"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f59e0b" stroke-width="3"
                            stroke-dasharray="13 87" stroke-dashoffset="-87"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-lg font-bold text-white">100%</div>
                        <div class="text-xs text-gray-500">total</div>
                    </div>
                </div>
            </div>
            <div class="space-y-2">
                @foreach([['#6c63ff','Desktop','52%'],['#00d4aa','Mobile','35%'],['#f59e0b','Tablet','13%']] as [$color,$device,$pct])
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:{{ $color }}"></div>
                        <span class="text-gray-400">{{ $device }}</span>
                    </div>
                    <span class="text-white font-semibold">{{ $pct }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- AI USAGE --}}
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
            <h2 class="font-semibold text-white mb-4">🤖 AI Usage</h2>
            <div class="space-y-4">
                <div class="bg-[#1a1a24] rounded-xl p-4">
                    <div class="flex justify-between text-xs mb-2">
                        <span class="text-gray-400">Claude Sonnet</span>
                        <span class="text-[#6c63ff] font-mono font-bold">1.24M tokens</span>
                    </div>
                    <div class="h-2 bg-[#0d0d14] rounded-full">
                        <div class="h-full bg-gradient-to-r from-[#6c63ff] to-purple-500 rounded-full" style="width:62%"></div>
                    </div>
                </div>
                <div class="bg-[#1a1a24] rounded-xl p-4">
                    <div class="flex justify-between text-xs mb-2">
                        <span class="text-gray-400">DeepSeek V3</span>
                        <span class="text-emerald-400 font-mono font-bold">780k tokens</span>
                    </div>
                    <div class="h-2 bg-[#0d0d14] rounded-full">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full" style="width:38%"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <div class="bg-[#1a1a24] rounded-xl p-3 text-center">
                        <div class="text-lg font-bold text-white font-display">$4.20</div>
                        <div class="text-xs text-gray-500">Claude cost</div>
                    </div>
                    <div class="bg-[#1a1a24] rounded-xl p-3 text-center">
                        <div class="text-lg font-bold text-white font-display">$0.21</div>
                        <div class="text-xs text-gray-500">DeepSeek cost</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT EVENTS --}}
    <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white">📋 Recent Events</h2>
            <button class="text-xs text-[#6c63ff] hover:underline">View all →</button>
        </div>
        <div class="space-y-0">
            @foreach([
                ['🚀','Project deployed','TaskFlow landing page deployed to ftp.taskflow.com','2 min ago','emerald'],
                ['🤖','AI generation','Built e-commerce product page (847 lines, 6 files)','8 min ago','purple'],
                ['👤','New user','sarah@studio.com registered via GitHub','15 min ago','blue'],
                ['💳','Marketplace sale','ShopKit Pro purchased by Marcus Dev — $29','1h ago','amber'],
                ['🔑','API key added','DeepSeek V3 key verified and saved','2h ago','cyan'],
                ['🛒','Item submitted','Restaurant Menu template submitted for review','3h ago','gray'],
            ] as [$icon,$title,$detail,$time,$color])
            <div class="flex items-start gap-4 py-3.5 border-b border-white/5 last:border-0">
                <div class="w-8 h-8 rounded-xl bg-{{ $color }}-500/15 flex items-center justify-center text-sm flex-shrink-0 mt-0.5">{{ $icon }}</div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-white font-medium">{{ $title }}</div>
                    <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $detail }}</div>
                </div>
                <span class="text-xs text-gray-600 flex-shrink-0 mt-1">{{ $time }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function analytics() {
    return { dateRange: '30d' }
}
</script>
@endpush
