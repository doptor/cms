@extends('layouts.app')
@section('title', 'Projects')

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="projectsList()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display font-bold text-2xl text-white">📁 Projects</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $projects->total() }} projects total</p>
        </div>
        <button @click="showCreate = true"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all shadow-lg shadow-[#6c63ff]/20">
            ＋ New Project
        </button>
    </div>

    {{-- FILTERS --}}
    <div class="flex items-center gap-3 mb-6 flex-wrap">
        <div class="flex items-center gap-2 bg-[#111118] border border-white/8 rounded-xl px-3 py-2 flex-1 max-w-xs">
            <span class="text-gray-600">🔍</span>
            <input x-model="search" type="text" placeholder="Search projects..."
                class="flex-1 bg-transparent text-sm text-gray-300 placeholder-gray-600 outline-none"/>
        </div>
        <div class="flex bg-[#111118] border border-white/8 rounded-xl p-1 gap-0.5">
            @foreach(['All','Draft','Ready','Deployed'] as $s)
            <button @click="statusFilter = '{{ strtolower($s) }}'"
                :class="statusFilter === '{{ strtolower($s) }}' ? 'bg-[#6c63ff] text-white' : 'text-gray-500 hover:text-gray-300'"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">{{ $s }}</button>
            @endforeach
        </div>
        <div class="flex bg-[#111118] border border-white/8 rounded-xl p-1 gap-0.5 ml-auto">
            <button @click="view = 'grid'" :class="view==='grid' ? 'bg-[#6c63ff] text-white' : 'text-gray-500'" class="p-1.5 rounded-lg transition-all">⊞</button>
            <button @click="view = 'list'" :class="view==='list' ? 'bg-[#6c63ff] text-white' : 'text-gray-500'" class="p-1.5 rounded-lg transition-all">☰</button>
        </div>
    </div>

    {{-- GRID VIEW --}}
    <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($projects as $project)
        <div class="bg-[#111118] border border-white/5 rounded-2xl overflow-hidden hover:border-[#6c63ff]/30 hover:-translate-y-0.5 transition-all group">
            {{-- Thumb --}}
            <div class="h-32 bg-gradient-to-br from-[#1a1a24] to-[#0a0a0f] flex items-center justify-center text-5xl relative">
                {{ match($project->type) { 'ecommerce'=>'🛍', 'blog'=>'📝', 'saas'=>'💼', 'landing_page'=>'🏠', 'portfolio'=>'🎨', 'booking'=>'📅', default=>'⚙️' } }}
                {{-- Status badge --}}
                <div class="absolute top-3 right-3 text-xs px-2 py-0.5 rounded-full
                    {{ $project->status==='deployed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/25' :
                       ($project->status==='ready'    ? 'bg-blue-500/20 text-blue-400 border border-blue-500/25' :
                       'bg-gray-500/15 text-gray-500 border border-gray-500/20') }}">
                    {{ ucfirst($project->status) }}
                </div>
                {{-- Hover actions --}}
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <a href="{{ route('projects.builder', $project) }}"
                        class="px-3 py-1.5 rounded-lg bg-[#6c63ff] text-white text-xs font-semibold hover:bg-[#5a52e0] transition-colors">Open →</a>
                    <a href="{{ route('deploy.show', $project) }}"
                        class="px-3 py-1.5 rounded-lg bg-emerald-500 text-black text-xs font-semibold hover:bg-emerald-400 transition-colors">🚀 Deploy</a>
                </div>
            </div>

            <div class="p-4">
                <h3 class="font-semibold text-white mb-1 truncate">{{ $project->name }}</h3>
                <p class="text-xs text-gray-500 mb-3">{{ ucfirst(str_replace('_',' ',$project->type)) }} · Updated {{ $project->updated_at->diffForHumans() }}</p>

                {{-- Stats row --}}
                <div class="flex items-center gap-3 text-xs text-gray-600">
                    <span title="Files">📄 {{ $project->files()->count() }}</span>
                    <span title="Messages">💬 {{ $project->chatHistory()->count() }}</span>
                    @if($project->domain)
                    <a href="http://{{ $project->domain }}" target="_blank" class="ml-auto text-[#6c63ff] hover:underline truncate">{{ $project->domain }}</a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-16 text-center">
                <div class="text-6xl mb-4">🚀</div>
                <h3 class="font-display font-bold text-white text-xl mb-2">No projects yet</h3>
                <p class="text-gray-500 text-sm mb-6">Create your first AI-powered app in seconds</p>
                <button @click="showCreate = true"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all">
                    ＋ Create First Project
                </button>
            </div>
        </div>
        @endforelse
    </div>

    {{-- LIST VIEW --}}
    <div x-show="view === 'list'" x-cloak class="bg-[#111118] border border-white/5 rounded-2xl overflow-hidden">
        <div class="grid grid-cols-12 px-6 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-white/5 bg-[#1a1a24]">
            <span class="col-span-4">Project</span>
            <span class="col-span-2">Type</span>
            <span class="col-span-2">Status</span>
            <span class="col-span-2">Updated</span>
            <span class="col-span-2">Actions</span>
        </div>
        @forelse($projects as $project)
        <div class="grid grid-cols-12 px-6 py-4 border-b border-white/5 last:border-0 hover:bg-white/2 transition-colors items-center group">
            <div class="col-span-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#1a1a24] flex items-center justify-center text-lg flex-shrink-0">
                    {{ match($project->type) { 'ecommerce'=>'🛍', 'blog'=>'📝', 'saas'=>'💼', 'landing_page'=>'🏠', 'portfolio'=>'🎨', default=>'⚙️' } }}
                </div>
                <div>
                    <div class="text-sm text-white font-medium">{{ $project->name }}</div>
                    <div class="text-xs text-gray-600 truncate max-w-xs">{{ $project->description ?: 'No description' }}</div>
                </div>
            </div>
            <div class="col-span-2 text-sm text-gray-400">{{ ucfirst(str_replace('_',' ',$project->type)) }}</div>
            <div class="col-span-2">
                <span class="text-xs px-2.5 py-1 rounded-full
                    {{ $project->status==='deployed' ? 'bg-emerald-500/15 text-emerald-400' : ($project->status==='ready' ? 'bg-blue-500/15 text-blue-400' : 'bg-gray-500/15 text-gray-500') }}">
                    {{ ucfirst($project->status) }}
                </span>
            </div>
            <div class="col-span-2 text-xs text-gray-500">{{ $project->updated_at->diffForHumans() }}</div>
            <div class="col-span-2 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('projects.builder', $project) }}" class="text-xs px-3 py-1.5 rounded-lg bg-[#6c63ff]/15 text-[#6c63ff] hover:bg-[#6c63ff]/25 transition-colors">Open</a>
                <a href="{{ route('deploy.show', $project) }}" class="text-xs px-3 py-1.5 rounded-lg bg-emerald-500/15 text-emerald-400 hover:bg-emerald-500/25 transition-colors">Deploy</a>
                <button @click="confirmDelete({{ $project->id }}, '{{ $project->name }}')" class="text-xs px-2 py-1.5 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors">🗑</button>
            </div>
        </div>
        @empty
        <div class="py-16 text-center text-gray-600 text-sm">No projects found</div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($projects->hasPages())
    <div class="mt-6">{{ $projects->links() }}</div>
    @endif

    {{-- CREATE MODAL --}}
    <div x-show="showCreate" x-cloak
        class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        @click.self="showCreate = false">
        <div class="bg-[#111118] border border-white/10 rounded-2xl p-8 w-full max-w-md shadow-2xl" @click.stop>
            <h2 class="font-display font-bold text-xl text-white mb-2">✨ New Project</h2>
            <p class="text-gray-500 text-sm mb-6">What are you building today?</p>

            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Project Name</label>
                <input x-model="newProject.name" type="text" placeholder="e.g. TaskFlow, My Blog..."
                    class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Type</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([['landing_page','🏠','Landing'],['blog','📝','Blog'],['ecommerce','🛍','Store'],['saas','💼','SaaS'],['portfolio','🎨','Portfolio'],['custom','⚙️','Custom']] as $t)
                    <button type="button" @click="newProject.type = '{{ $t[0] }}'"
                        :class="newProject.type === '{{ $t[0] }}' ? 'border-[#6c63ff] bg-[#6c63ff]/10 text-white' : 'border-white/10 text-gray-500 hover:border-white/20'"
                        class="border-2 rounded-xl p-3 text-center cursor-pointer transition-all">
                        <div class="text-xl mb-1">{{ $t[1] }}</div>
                        <div class="text-xs font-medium">{{ $t[2] }}</div>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Description</label>
                <textarea x-model="newProject.description" rows="2" placeholder="Brief description..."
                    class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors resize-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button @click="showCreate = false" class="flex-1 py-2.5 rounded-xl border border-white/10 text-sm text-gray-400 hover:border-white/20 transition-all">Cancel</button>
                <button @click="createProject()" :disabled="!newProject.name || creating"
                    class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 disabled:opacity-40 transition-all flex items-center justify-center gap-2">
                    <span x-show="creating" class="animate-spin">⟳</span>
                    <span x-text="creating ? 'Creating...' : 'Create Project'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- DELETE CONFIRM --}}
    <div x-show="deleteConfirm.show" x-cloak class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="deleteConfirm.show = false">
        <div class="bg-[#111118] border border-red-500/20 rounded-2xl p-6 w-full max-w-sm shadow-2xl text-center" @click.stop>
            <div class="text-4xl mb-3">⚠️</div>
            <h3 class="font-display font-bold text-white mb-2">Delete Project?</h3>
            <p class="text-gray-500 text-sm mb-6">This will permanently delete <strong class="text-white" x-text="deleteConfirm.name"></strong> and all its files. This cannot be undone.</p>
            <div class="flex gap-3">
                <button @click="deleteConfirm.show = false" class="flex-1 py-2.5 rounded-xl border border-white/10 text-sm text-gray-400">Cancel</button>
                <button @click="deleteProject()" class="flex-1 py-2.5 rounded-xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600 transition-colors">Delete Forever</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function projectsList() {
    return {
        search: '', statusFilter: 'all', view: 'grid',
        showCreate: false, creating: false,
        newProject: { name: '', type: 'landing_page', description: '' },
        deleteConfirm: { show: false, id: null, name: '' },

        confirmDelete(id, name) { this.deleteConfirm = { show: true, id, name }; },

        async createProject() {
            this.creating = true;
            try {
                const data = await apiPost('/projects', this.newProject);
                if (data.redirect) window.location.href = data.redirect;
                else showToast(data.message || 'Error creating project', 'error');
            } catch { showToast('Network error', 'error'); }
            finally { this.creating = false; }
        },

        async deleteProject() {
            const id = this.deleteConfirm.id;
            this.deleteConfirm.show = false;
            try {
                const data = await apiDelete('/projects/' + id);
                if (data.success) { showToast('Project deleted'); location.reload(); }
            } catch { showToast('Error deleting project', 'error'); }
        }
    }
}
</script>
@endpush
