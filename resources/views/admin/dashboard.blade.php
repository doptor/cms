@extends('layouts.app')
@section('title', 'Admin Panel')

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="adminPanel()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display font-bold text-2xl text-white">🛡 Admin Panel</h1>
            <p class="text-gray-500 text-sm mt-1">Platform management & oversight</p>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-red-500/10 border border-red-500/20 rounded-xl text-xs text-red-400">
            🔒 Admin Access Only
        </div>
    </div>

    {{-- PLATFORM STATS --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @foreach([
            ['icon'=>'👥','label'=>'Total Users','value'=>'2,841','change'=>'+12%'],
            ['icon'=>'📁','label'=>'Total Projects','value'=>'8,294','change'=>'+24%'],
            ['icon'=>'🚀','label'=>'Deployed Sites','value'=>'1,203','change'=>'+8%'],
            ['icon'=>'🤖','label'=>'AI Requests Today','value'=>'45,291','change'=>'+31%'],
            ['icon'=>'💰','label'=>'Marketplace GMV','value'=>'$12,480','change'=>'+18%'],
        ] as $stat)
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
            <div class="text-xl mb-2">{{ $stat['icon'] }}</div>
            <div class="text-xl font-bold text-white font-display">{{ $stat['value'] }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</div>
            <div class="text-xs text-emerald-400 mt-1">{{ $stat['change'] }} this month</div>
        </div>
        @endforeach
    </div>

    {{-- TABS --}}
    <div class="flex gap-0 border-b border-white/5 mb-6">
        @foreach(['Users','Marketplace','AI Usage','System'] as $tab)
        <button @click="activeTab = '{{ strtolower($tab) }}'"
            :class="activeTab === '{{ strtolower($tab) }}' ? 'text-[#6c63ff] border-b-2 border-[#6c63ff]' : 'text-gray-500 hover:text-gray-300'"
            class="px-5 py-3 text-sm font-medium transition-all">{{ $tab }}</button>
        @endforeach
    </div>

    {{-- USERS TAB --}}
    <div x-show="activeTab === 'users'">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 bg-[#111118] border border-white/10 rounded-xl px-4 py-2 w-72">
                <span class="text-gray-500 text-sm">🔍</span>
                <input type="text" placeholder="Search users..." class="bg-transparent text-sm text-gray-300 placeholder-gray-600 outline-none flex-1"/>
            </div>
            <select class="bg-[#111118] border border-white/10 rounded-xl px-4 py-2 text-sm text-gray-400 outline-none cursor-pointer">
                <option>All Users</option>
                <option>Admins</option>
                <option>Active</option>
                <option>Suspended</option>
            </select>
        </div>

        <div class="bg-[#111118] border border-white/5 rounded-2xl overflow-hidden">
            <div class="grid grid-cols-6 px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-white/5 bg-[#1a1a24]">
                <span class="col-span-2">User</span>
                <span>Projects</span>
                <span>AI Tokens</span>
                <span>Status</span>
                <span>Actions</span>
            </div>
            @foreach([
                ['name'=>'Ahmed Ryaan','email'=>'ahmed@example.com','projects'=>12,'tokens'=>'245k','status'=>'active','role'=>'admin'],
                ['name'=>'Sarah Chen','email'=>'sarah@example.com','projects'=>8,'tokens'=>'89k','status'=>'active','role'=>'user'],
                ['name'=>'Marcus Dev','email'=>'marcus@example.com','projects'=>24,'tokens'=>'1.2M','status'=>'active','role'=>'user'],
                ['name'=>'Priya Singh','email'=>'priya@example.com','projects'=>3,'tokens'=>'12k','status'=>'inactive','role'=>'user'],
                ['name'=>'Tom Wilson','email'=>'tom@example.com','projects'=>17,'tokens'=>'340k','status'=>'active','role'=>'user'],
            ] as $user)
            <div class="grid grid-cols-6 px-6 py-4 border-b border-white/5 last:border-0 hover:bg-white/2 transition-colors items-center">
                <div class="col-span-2 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#6c63ff] to-purple-600 flex items-center justify-center text-xs font-bold text-white">{{ substr($user['name'],0,1) }}</div>
                    <div>
                        <div class="text-sm text-white font-medium">{{ $user['name'] }}
                            @if($user['role']==='admin')<span class="ml-1.5 text-xs bg-[#6c63ff]/20 text-[#6c63ff] px-1.5 py-0.5 rounded">Admin</span>@endif
                        </div>
                        <div class="text-xs text-gray-500">{{ $user['email'] }}</div>
                    </div>
                </div>
                <div class="text-sm text-gray-300">{{ $user['projects'] }}</div>
                <div class="text-sm text-gray-300 font-mono">{{ $user['tokens'] }}</div>
                <div>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $user['status']==='active' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-gray-500/15 text-gray-500' }}">
                        {{ ucfirst($user['status']) }}
                    </span>
                </div>
                <div class="flex gap-2">
                    <button class="text-xs px-2 py-1 rounded border border-white/10 text-gray-400 hover:border-white/20 transition-all">View</button>
                    <button class="text-xs px-2 py-1 rounded border border-red-500/30 text-red-400 hover:bg-red-500/10 transition-all">
                        {{ $user['status']==='active' ? 'Suspend' : 'Restore' }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- MARKETPLACE TAB --}}
    <div x-show="activeTab === 'marketplace'" x-cloak>
        <h2 class="font-semibold text-white mb-4">⏳ Pending Approval</h2>
        <div class="space-y-3">
            @foreach([
                ['name'=>'BookingPro','author'=>'Dev Studio','category'=>'booking','price'=>'$39','submitted'=>'2h ago'],
                ['name'=>'NewsletterKit','author'=>'Sarah Chen','category'=>'saas','price'=>'FREE','submitted'=>'5h ago'],
                ['name'=>'Restaurant Menu','author'=>'Marcus Dev','category'=>'landing','price'=>'$19','submitted'=>'1d ago'],
            ] as $item)
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#1a1a24] flex items-center justify-center text-2xl flex-shrink-0">📦</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-semibold text-white">{{ $item['name'] }}</span>
                        <span class="text-xs text-gray-500">by {{ $item['author'] }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-[#1a1a24] text-gray-400">{{ $item['category'] }}</span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span>Price: <strong class="text-white">{{ $item['price'] }}</strong></span>
                        <span>Submitted {{ $item['submitted'] }}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-2 rounded-xl text-xs border border-white/10 text-gray-400 hover:border-white/20 transition-all">👁 Review</button>
                    <button class="px-4 py-2 rounded-xl text-xs bg-emerald-500/15 border border-emerald-500/25 text-emerald-400 hover:bg-emerald-500/25 transition-all">✅ Approve</button>
                    <button class="px-4 py-2 rounded-xl text-xs bg-red-500/10 border border-red-500/25 text-red-400 hover:bg-red-500/20 transition-all">❌ Reject</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- AI USAGE TAB --}}
    <div x-show="activeTab === 'ai usage'" x-cloak>
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-4">Model Usage (This Month)</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-400">Claude Sonnet</span>
                            <span class="text-white font-mono">12.4M tokens</span>
                        </div>
                        <div class="h-3 bg-[#1a1a24] rounded-full"><div class="h-full w-[62%] bg-gradient-to-r from-[#6c63ff] to-purple-500 rounded-full"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-400">DeepSeek V3</span>
                            <span class="text-white font-mono">7.8M tokens</span>
                        </div>
                        <div class="h-3 bg-[#1a1a24] rounded-full"><div class="h-full w-[38%] bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full"></div></div>
                    </div>
                </div>
            </div>
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-4">Top AI Users</h3>
                <div class="space-y-3">
                    @foreach([['Marcus Dev','1.2M'],['Ahmed Ryaan','245k'],['Sarah Chen','89k']] as $u)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-300">{{ $u[0] }}</span>
                        <span class="text-sm font-mono text-[#6c63ff]">{{ $u[1] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- SYSTEM TAB --}}
    <div x-show="activeTab === 'system'" x-cloak>
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-4">🖥 Server Status</h3>
                <div class="space-y-3 text-sm">
                    @foreach([
                        ['PHP Version','8.2.10','✅'],
                        ['Laravel Version','11.x','✅'],
                        ['MySQL','Connected','✅'],
                        ['Storage','2.4 GB / 10 GB','⚠️'],
                        ['Cache Driver','File','✅'],
                        ['Queue Driver','Database','✅'],
                    ] as $sys)
                    <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                        <span class="text-gray-400">{{ $sys[0] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-300 font-mono text-xs">{{ $sys[1] }}</span>
                            <span>{{ $sys[2] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <h3 class="font-semibold text-white mb-4">⚡ Quick Admin Actions</h3>
                <div class="space-y-2">
                    @foreach([
                        ['🗑 Clear Application Cache','Clear cache'],
                        ['📊 Run Database Optimize','Optimize'],
                        ['📧 Test Email Config','Test Mail'],
                        ['🔄 Rebuild Sitemap','Rebuild'],
                        ['🔒 Force All Logout','Force Logout'],
                    ] as $action)
                    <button class="w-full flex items-center justify-between px-4 py-3 rounded-xl border border-white/5 text-sm text-gray-400 hover:border-white/15 hover:text-gray-300 transition-all group">
                        <span>{{ $action[0] }}</span>
                        <span class="text-xs text-[#6c63ff] opacity-0 group-hover:opacity-100 transition-opacity">Run →</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function adminPanel() {
    return { activeTab: 'users' }
}
</script>
@endpush
