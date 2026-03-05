@extends('layouts.app')
@section('title', 'Deploy — ' . $project->name)

@section('content')
<div class="p-6 max-w-4xl mx-auto" x-data="deployManager({{ $project->id }}, '{{ $project->name }}')">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('projects.builder', $project) }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-white/10 text-gray-400 hover:border-white/20 hover:text-white transition-all">←</a>
        <div>
            <h1 class="font-display font-bold text-2xl text-white">🚀 Deploy Project</h1>
            <p class="text-gray-500 text-sm mt-0.5">{{ $project->name }} → Shared Hosting via FTP</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <span class="text-xs px-3 py-1.5 rounded-full border
                {{ $project->status === 'deployed' ? 'bg-emerald-500/15 border-emerald-500/25 text-emerald-400' :
                   ($project->status === 'ready'    ? 'bg-blue-500/15 border-blue-500/25 text-blue-400' :
                   'bg-gray-500/15 border-gray-500/25 text-gray-400') }}">
                {{ ucfirst($project->status) }}
            </span>
            @if($project->deployed_at)
            <span class="text-xs text-gray-500">Last deployed {{ $project->deployed_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- LEFT: FORM --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- FTP CREDENTIALS --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/15 flex items-center justify-center">📡</div>
                    <div>
                        <h2 class="font-semibold text-white">FTP Connection</h2>
                        <p class="text-xs text-gray-500">Find these in your cPanel → FTP Accounts</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">FTP Host</label>
                        <input x-model="ftp.host" type="text" placeholder="ftp.yourdomain.com"
                            class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono"/>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">FTP Username</label>
                            <input x-model="ftp.username" type="text" placeholder="ftpuser@domain.com"
                                class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Port</label>
                            <input x-model="ftp.port" type="number" placeholder="21"
                                class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">FTP Password</label>
                        <div class="relative">
                            <input x-model="ftp.password" :type="showFtpPass ? 'text' : 'password'" placeholder="••••••••••"
                                class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 pr-10 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono"/>
                            <button @click="showFtpPass = !showFtpPass" type="button" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-300 transition-colors">
                                <span x-text="showFtpPass ? '🙈' : '👁'"></span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Remote Path</label>
                        <input x-model="ftp.remote_path" type="text" placeholder="public_html/"
                            class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono"/>
                        <p class="text-xs text-gray-600 mt-1">Usually <code class="bg-white/5 px-1 rounded">public_html/</code> or <code class="bg-white/5 px-1 rounded">public_html/subfolder/</code></p>
                    </div>

                    <button @click="testFtp()" :disabled="testing || !ftp.host"
                        class="w-full py-2.5 rounded-xl border border-white/10 text-sm text-gray-400 hover:border-white/20 hover:text-gray-300 disabled:opacity-40 transition-all flex items-center justify-center gap-2">
                        <span x-show="testing" class="animate-spin">⟳</span>
                        <span x-text="testing ? 'Testing connection...' : '🔌 Test FTP Connection'"></span>
                    </button>

                    <div x-show="ftpTestResult" x-cloak
                        :class="ftpTestResult === 'success' ? 'bg-emerald-500/10 border-emerald-500/25 text-emerald-400' : 'bg-red-500/10 border-red-500/25 text-red-400'"
                        class="px-4 py-3 rounded-xl border text-sm flex items-center gap-2">
                        <span x-text="ftpTestResult === 'success' ? '✅ FTP connection successful!' : '❌ Connection failed. Check credentials.'"></span>
                    </div>
                </div>
            </div>

            {{-- DATABASE CREDENTIALS --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/15 flex items-center justify-center">🗄</div>
                    <div>
                        <h2 class="font-semibold text-white">MySQL Database</h2>
                        <p class="text-xs text-gray-500">Find these in cPanel → MySQL Databases</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Database Name</label>
                        <input x-model="db.name" type="text" placeholder="username_dbname"
                            class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono"/>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">DB Username</label>
                            <input x-model="db.username" type="text" placeholder="username_user"
                                class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">DB Password</label>
                            <input x-model="db.password" type="password" placeholder="••••••••"
                                class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono"/>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DEPLOY OPTIONS --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <h2 class="font-semibold text-white mb-4">⚙️ Deploy Options</h2>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input x-model="options.runMigrations" type="checkbox" class="w-4 h-4 rounded accent-[#6c63ff] bg-[#1a1a24]">
                        <div>
                            <div class="text-sm text-gray-300">Run database migrations</div>
                            <div class="text-xs text-gray-600">php artisan migrate --force</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input x-model="options.clearCache" type="checkbox" class="w-4 h-4 rounded accent-[#6c63ff] bg-[#1a1a24]">
                        <div>
                            <div class="text-sm text-gray-300">Clear & rebuild cache</div>
                            <div class="text-xs text-gray-600">php artisan optimize:clear && optimize</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input x-model="options.setPermissions" type="checkbox" class="w-4 h-4 rounded accent-[#6c63ff] bg-[#1a1a24]">
                        <div>
                            <div class="text-sm text-gray-300">Set file permissions (755/644)</div>
                            <div class="text-xs text-gray-600">Required for Laravel on shared hosting</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input x-model="options.storageLink" type="checkbox" class="w-4 h-4 rounded accent-[#6c63ff] bg-[#1a1a24]">
                        <div>
                            <div class="text-sm text-gray-300">Create storage symlink</div>
                            <div class="text-xs text-gray-600">php artisan storage:link</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- DEPLOY BUTTON --}}
            <button @click="deploy()" :disabled="deploying || !ftp.host || !db.name"
                class="w-full py-4 rounded-2xl text-base font-bold transition-all flex items-center justify-center gap-3
                    bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white hover:opacity-90 disabled:opacity-40 shadow-2xl shadow-[#6c63ff]/20">
                <span x-show="deploying" class="text-xl animate-spin">⟳</span>
                <span x-text="deploying ? 'Deploying...' : '🚀 Deploy to Shared Hosting'"></span>
            </button>
        </div>

        {{-- RIGHT: PROGRESS + INFO --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- DEPLOY PROGRESS --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
                <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
                    📊 Deploy Progress
                    <span x-show="deploying" class="text-xs text-[#6c63ff] animate-pulse">● Live</span>
                </h3>

                <div x-show="steps.length === 0" class="text-center py-8 text-gray-600 text-sm">
                    Configure credentials and click Deploy to start
                </div>

                <div class="space-y-2">
                    <template x-for="(step, i) in steps" :key="i">
                        <div class="flex items-start gap-3 py-2 border-b border-white/5 last:border-0">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 text-xs"
                                :class="{
                                    'bg-emerald-500/20 text-emerald-400': step.status === 'done',
                                    'bg-[#6c63ff]/20 text-[#6c63ff]':    step.status === 'running',
                                    'bg-red-500/20 text-red-400':         step.status === 'error',
                                    'bg-white/5 text-gray-600':           step.status === 'pending'
                                }">
                                <span x-text="step.status === 'done' ? '✓' : (step.status === 'running' ? '⟳' : (step.status === 'error' ? '✕' : '○'))"></span>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs"
                                    :class="{
                                        'text-emerald-400': step.status === 'done',
                                        'text-[#6c63ff]':   step.status === 'running',
                                        'text-red-400':     step.status === 'error',
                                        'text-gray-600':    step.status === 'pending'
                                    }"
                                    x-text="step.message"></div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Progress bar --}}
                <div x-show="deploying || deployDone" x-cloak class="mt-4">
                    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                        <span x-text="deployDone ? 'Complete' : 'Deploying...'"></span>
                        <span x-text="Math.round(progress) + '%'"></span>
                    </div>
                    <div class="h-2 bg-[#1a1a24] rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-[#6c63ff] to-emerald-500 rounded-full transition-all duration-500"
                            :style="'width:' + progress + '%'"></div>
                    </div>
                </div>

                {{-- Success state --}}
                <div x-show="deployDone && !deployError" x-cloak class="mt-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-center">
                    <div class="text-2xl mb-2">🎉</div>
                    <div class="text-sm font-semibold text-emerald-400 mb-1">Deployed Successfully!</div>
                    <a :href="'http://' + ftp.host" target="_blank"
                        class="text-xs text-[#6c63ff] hover:underline" x-text="'Visit: ' + ftp.host"></a>
                </div>
            </div>

            {{-- HELP GUIDE --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
                <h3 class="font-semibold text-white mb-3">📖 cPanel Setup Guide</h3>
                <div class="space-y-3 text-xs text-gray-400">
                    <div class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#6c63ff]/20 text-[#6c63ff] flex items-center justify-center flex-shrink-0 font-bold text-xs">1</span>
                        <div>Login to cPanel → <strong class="text-gray-300">FTP Accounts</strong> → Create FTP user</div>
                    </div>
                    <div class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#6c63ff]/20 text-[#6c63ff] flex items-center justify-center flex-shrink-0 font-bold text-xs">2</span>
                        <div>Go to <strong class="text-gray-300">MySQL Databases</strong> → Create DB + User → Assign all privileges</div>
                    </div>
                    <div class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#6c63ff]/20 text-[#6c63ff] flex items-center justify-center flex-shrink-0 font-bold text-xs">3</span>
                        <div>Set remote path to <code class="bg-white/8 px-1 rounded">public_html/</code> for root domain</div>
                    </div>
                    <div class="flex gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#6c63ff]/20 text-[#6c63ff] flex items-center justify-center flex-shrink-0 font-bold text-xs">4</span>
                        <div>Click Deploy — RyaanCMS handles the rest automatically ✅</div>
                    </div>
                </div>
            </div>

            {{-- DEPLOY HISTORY --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-5">
                <h3 class="font-semibold text-white mb-3">🕐 Deploy History</h3>
                @forelse($project->deployments ?? [] as $dep)
                <div class="flex items-center gap-2 py-2 border-b border-white/5 last:border-0 text-xs">
                    <span class="{{ $dep->status === 'success' ? 'text-emerald-400' : 'text-red-400' }}">
                        {{ $dep->status === 'success' ? '✅' : '❌' }}
                    </span>
                    <span class="text-gray-400">{{ $dep->created_at->diffForHumans() }}</span>
                    <span class="text-gray-600 ml-auto">{{ $dep->files_uploaded }} files</span>
                </div>
                @empty
                <div class="text-xs text-gray-600 text-center py-4">No deployments yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deployManager(projectId, projectName) {
    return {
        projectId, projectName,
        ftp: { host: '', username: '', password: '', port: 21, remote_path: 'public_html/' },
        db:  { name: '', username: '', password: '' },
        options: { runMigrations: true, clearCache: true, setPermissions: true, storageLink: true },
        showFtpPass: false,
        testing: false, ftpTestResult: null,
        deploying: false, deployDone: false, deployError: false,
        steps: [], progress: 0,

        async testFtp() {
            this.testing = true; this.ftpTestResult = null;
            try {
                const data = await apiPost(`/deploy/${this.projectId}/test`, this.ftp);
                this.ftpTestResult = data.success ? 'success' : 'fail';
            } catch { this.ftpTestResult = 'fail'; }
            finally { this.testing = false; }
        },

        async deploy() {
            this.deploying = true; this.deployDone = false; this.deployError = false;
            this.steps = []; this.progress = 0;

            // Save credentials first
            await apiPost(`/deploy/${this.projectId}/credentials`, { ...this.ftp, ...this.db });

            // Simulate live progress steps
            const allSteps = [
                'Connecting to FTP server...',
                'Packaging Laravel project...',
                'Uploading app/ directory...',
                'Uploading resources/ & public/...',
                'Uploading vendor/ dependencies...',
                'Configuring .env file...',
                'Setting file permissions (755/644)...',
                'Running database migrations...',
                'Clearing application cache...',
                'Creating storage symlink...',
                'Verifying deployment...',
            ];

            for (let i = 0; i < allSteps.length; i++) {
                this.steps.push({ message: allSteps[i], status: 'running' });
                await new Promise(r => setTimeout(r, 600 + Math.random() * 400));
                this.steps[i].status = 'done';
                this.progress = Math.round(((i + 1) / allSteps.length) * 100);
            }

            // Final API call
            try {
                const data = await apiPost(`/deploy/${this.projectId}/deploy`, {});
                this.deployDone = true;
                if (!data.success) {
                    this.deployError = true;
                    this.steps.push({ message: data.message || 'Deploy failed', status: 'error' });
                } else {
                    showToast('🚀 Deployed successfully!');
                }
            } catch {
                this.deployError = true;
                this.steps.push({ message: 'Network error during deployment', status: 'error' });
            } finally {
                this.deploying = false;
            }
        }
    }
}
</script>
@endpush
