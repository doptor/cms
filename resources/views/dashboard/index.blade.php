@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="dashboard()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display font-bold text-2xl text-white">
                Good {{ $greeting }}, {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-gray-500 text-sm mt-1">Here's what's happening in your workspace today.</p>
        </div>
        <button @click="showNewProject = true"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all shadow-lg shadow-[#6c63ff]/20">
            ＋ New Project
        </button>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $stats = [
                ['icon'=>'📁', 'label'=>'Total Projects', 'value'=>$total_projects, 'color'=>'from-[#6c63ff]/20 to-purple-500/10', 'border'=>'border-[#6c63ff]/20'],
                ['icon'=>'🚀', 'label'=>'Deployed', 'value'=>$deployed_projects, 'color'=>'from-emerald-500/20 to-teal-500/10', 'border'=>'border-emerald-500/20'],
                ['icon'=>'🤖', 'label'=>'AI Requests Today', 'value'=>number_format($today_requests), 'color'=>'from-blue-500/20 to-cyan-500/10', 'border'=>'border-blue-500/20'],
                ['icon'=>'⚡', 'label'=>'Tokens This Month', 'value'=>number_format($month_tokens), 'color'=>'from-amber-500/20 to-orange-500/10', 'border'=>'border-amber-500/20'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-gradient-to-br {{ $stat['color'] }} border {{ $stat['border'] }} rounded-2xl p-5">
            <div class="text-2xl mb-3">{{ $stat['icon'] }}</div>
            <div class="text-2xl font-bold text-white font-display mb-1">{{ $stat['value'] }}</div>
            <div class="text-xs text-gray-500">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- RECENT PROJECTS --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-bold text-white">Recent Projects</h2>
                <a href="{{ route('projects.index') }}" class="text-xs text-[#6c63ff] hover:underline">View all →</a>
            </div>

            @if($recent_projects->isEmpty())
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-12 text-center">
                <div class="text-5xl mb-4">🚀</div>
                <h3 class="font-display font-bold text-white mb-2">Build your first app</h3>
                <p class="text-gray-500 text-sm mb-6">Describe what you want to build and AI will create it for you</p>
                <button @click="showNewProject = true"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all">
                    ＋ Create First Project
                </button>
            </div>
            @else
            <div class="space-y-3">
                @foreach($recent_projects as $project)
                <div class="bg-[#111118] border border-white/5 rounded-2xl p-4 hover:border-[#6c63ff]/30 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#6c63ff]/30 to-purple-500/20 flex items-center justify-center text-lg flex-shrink-0">
                            {{ match($project->type) {
                                'ecommerce' => '🛍',
                                'blog' => '📝',
                                'saas' => '💼',
                                'landing_page' => '🏠',
                                'portfolio' => '🎨',
                                default => '⚙️'
                            } }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <h3 class="font-semibold text-white text-sm truncate">{{ $project->name }}</h3>
                                <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0
                                    {{ $project->status === 'deployed' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/25' :
                                       ($project->status === 'ready' ? 'bg-blue-500/15 text-blue-400 border border-blue-500/25' :
                                       'bg-gray-500/15 text-gray-400 border border-gray-500/25') }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_',' ',$project->type)) }} · Updated {{ $project->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('projects.builder', $project) }}"
                                class="text-xs px-3 py-1.5 rounded-lg bg-[#6c63ff]/20 text-[#6c63ff] hover:bg-[#6c63ff]/30 transition-colors">
                                Open →
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="space-y-6">

            {{-- API KEY STATUS --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
                <h3 class="font-semibold text-white text-sm mb-4">🔑 AI Engines</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ auth()->user()->hasClaudeKey() ? 'bg-emerald-400 shadow-sm shadow-emerald-400/60' : 'bg-gray-600' }}"></div>
                            <span class="text-sm text-gray-300">Claude Sonnet</span>
                        </div>
                        @if(auth()->user()->hasClaudeKey())
                            <span class="text-xs text-emerald-400">Active</span>
                        @else
                            <a href="{{ route('settings.index') }}" class="text-xs text-[#6c63ff] hover:underline">Add key →</a>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ auth()->user()->hasDeepSeekKey() ? 'bg-emerald-400 shadow-sm shadow-emerald-400/60' : 'bg-gray-600' }}"></div>
                            <span class="text-sm text-gray-300">DeepSeek V3</span>
                        </div>
                        @if(auth()->user()->hasDeepSeekKey())
                            <span class="text-xs text-emerald-400">Active</span>
                        @else
                            <a href="{{ route('settings.index') }}" class="text-xs text-[#6c63ff] hover:underline">Add key →</a>
                        @endif
                    </div>
                </div>
                @unless(auth()->user()->hasAnyAiKey())
                <a href="{{ route('settings.index') }}" class="mt-4 block w-full text-center py-2 rounded-xl border border-[#6c63ff]/30 text-[#6c63ff] text-xs hover:bg-[#6c63ff]/10 transition-colors">
                    ＋ Add API Keys to Start Building
                </a>
                @endunless
            </div>

            {{-- TOKEN USAGE --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
                <h3 class="font-semibold text-white text-sm mb-4">📊 Usage This Month</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-gray-500">Claude Tokens</span>
                            <span class="text-gray-300 font-mono">{{ number_format($claude_tokens) }}</span>
                        </div>
                        <div class="h-1.5 bg-[#1a1a24] rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#6c63ff] to-purple-500 rounded-full" style="width:{{ min(100, ($claude_tokens/500000)*100) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-gray-500">DeepSeek Tokens</span>
                            <span class="text-gray-300 font-mono">{{ number_format($deepseek_tokens) }}</span>
                        </div>
                        <div class="h-1.5 bg-[#1a1a24] rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full" style="width:{{ min(100, ($deepseek_tokens/1000000)*100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
                <h3 class="font-semibold text-white text-sm mb-4">⚡ Quick Actions</h3>
                <div class="space-y-2">
                    @foreach([
                        ['href'=>route('marketplace.index'), 'icon'=>'🛒', 'label'=>'Browse Marketplace'],
                        ['href'=>route('settings.index'),    'icon'=>'🔑', 'label'=>'API Key Settings'],
                        ['href'=>'#',                        'icon'=>'📖', 'label'=>'Documentation'],
                        ['href'=>'https://github.com',       'icon'=>'⭐', 'label'=>'Star on GitHub'],
                    ] as $action)
                    <a href="{{ $action['href'] }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-white/5 transition-all group">
                        <span>{{ $action['icon'] }}</span>
                        <span>{{ $action['label'] }}</span>
                        <span class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity text-[#6c63ff]">→</span>
                    </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

{{-- NEW PROJECT MODAL --}}
<div x-show="showNewProject" x-cloak
    class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="showNewProject = false">
    <div class="bg-[#111118] border border-white/10 rounded-2xl p-8 w-full max-w-md shadow-2xl" @click.stop>
        <h2 class="font-display font-bold text-xl text-white mb-2">New Project</h2>
        <p class="text-gray-500 text-sm mb-6">What are you building today?</p>

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Project Name</label>
            <input x-model="newProject.name" type="text" placeholder="e.g. TaskFlow, My Portfolio..."
                class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Project Type</label>
            <div class="grid grid-cols-3 gap-2">
                @foreach([
                    ['value'=>'landing_page','icon'=>'🏠','label'=>'Landing Page'],
                    ['value'=>'blog','icon'=>'📝','label'=>'Blog'],
                    ['value'=>'ecommerce','icon'=>'🛍','label'=>'E-Commerce'],
                    ['value'=>'saas','icon'=>'💼','label'=>'SaaS'],
                    ['value'=>'portfolio','icon'=>'🎨','label'=>'Portfolio'],
                    ['value'=>'custom','icon'=>'⚙️','label'=>'Custom'],
                ] as $type)
                <button type="button"
                    @click="newProject.type = '{{ $type['value'] }}'"
                    :class="newProject.type === '{{ $type['value'] }}' ? 'border-[#6c63ff] bg-[#6c63ff]/10 text-white' : 'border-white/10 text-gray-400 hover:border-white/20'"
                    class="border-2 rounded-xl p-3 text-center cursor-pointer transition-all">
                    <div class="text-xl mb-1">{{ $type['icon'] }}</div>
                    <div class="text-xs font-medium">{{ $type['label'] }}</div>
                </button>
                @endforeach
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Description (optional)</label>
            <textarea x-model="newProject.description" rows="2" placeholder="Brief description..."
                class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors resize-none"></textarea>
        </div>

        <div class="flex gap-3">
            <button @click="showNewProject = false" class="flex-1 py-2.5 rounded-xl border border-white/10 text-sm text-gray-400 hover:border-white/20 transition-all">Cancel</button>
            <button @click="createProject()" :disabled="!newProject.name || creating"
                class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 disabled:opacity-40 transition-all flex items-center justify-center gap-2">
                <span x-show="creating" class="animate-spin">⟳</span>
                <span x-text="creating ? 'Creating...' : 'Create Project'"></span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboard() {
    return {
        showNewProject: false,
        creating: false,
        newProject: { name: '', type: 'landing_page', description: '' },

        async createProject() {
            if (!this.newProject.name) return;
            this.creating = true;
            try {
                const data = await apiPost('/projects', this.newProject);
                if (data.redirect) window.location.href = data.redirect;
                else showToast(data.message || 'Error', 'error');
            } catch(e) { showToast('Network error', 'error'); }
            finally { this.creating = false; }
        }
    }
}
</script>
@endpush
