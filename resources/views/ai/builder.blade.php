@extends('layouts.app')
@section('title', 'AI Builder — ' . $project->name)

@push('styles')
<style>
    .builder-layout { display: grid; grid-template-columns: 1fr 1fr; height: calc(100vh - 112px); }
    .chat-panel, .preview-panel { display: flex; flex-direction: column; overflow: hidden; }
    .chat-messages { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 14px; }
    .code-block { background: #0d0d14; border: 1px solid #222232; border-radius: 10px; overflow: hidden; margin-top: 8px; }
    .code-header { display: flex; align-items: center; justify-content: space-between; padding: 8px 14px; background: #1a1a24; border-bottom: 1px solid #222232; }
    .code-content { padding: 14px; font-family: 'DM Mono', monospace; font-size: 12px; line-height: 1.7; overflow-x: auto; white-space: pre; color: #a8b3cf; max-height: 300px; overflow-y: auto; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner { animation: spin 1s linear infinite; display: inline-block; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .msg-animate { animation: fadeUp 0.25s ease; }
    .thinking-dot { animation: pulse 1.2s ease infinite; }
    .thinking-dot:nth-child(2) { animation-delay: 0.2s; }
    .thinking-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes pulse { 0%, 100% { opacity: 0.3; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.2); } }
</style>
@endpush

@section('content')
<div class="builder-layout border-t border-white/5">

    {{-- ═══ LEFT: AI CHAT PANEL ═══ --}}
    <div class="chat-panel border-r border-white/5" x-data="aiBuilder({{ $project->id }})">

        {{-- Panel Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-white/5 bg-[#111118] flex-shrink-0">
            <div class="flex items-center gap-2">
                <span class="text-sm font-display font-bold">🤖 AI Developer</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-ryaan-500/20 text-ryaan-400 border border-ryaan-500/30 font-mono">Claude</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 font-mono">DeepSeek</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 font-mono" x-text="project.name"></span>
                <button @click="clearChat()" class="text-xs px-2 py-1 rounded border border-white/10 text-gray-400 hover:text-white hover:border-white/20 transition-all">🗑 Clear</button>
            </div>
        </div>

        {{-- Messages --}}
        <div class="chat-messages" id="chatMessages">

            {{-- Welcome --}}
            <div class="bg-ryaan-500/8 border border-ryaan-500/15 rounded-xl p-4 text-sm text-gray-400 leading-relaxed msg-animate">
                👋 <strong class="text-white">RyaanCMS AI Developer ready.</strong><br>
                Describe what you want to build for <strong class="text-ryaan-400">{{ $project->name }}</strong>. I can write full Laravel code, database schemas, UI components, fix bugs, and deploy to your shared hosting.
            </div>

            {{-- Dynamic messages --}}
            <template x-for="(msg, i) in messages" :key="i">
                <div class="flex gap-3 msg-animate">
                    {{-- Avatar --}}
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs flex-shrink-0 mt-0.5"
                         :class="msg.role === 'user' ? 'bg-gradient-to-br from-ryaan-500 to-purple-500' : 'bg-gradient-to-br from-emerald-500 to-teal-600'">
                        <span x-text="msg.role === 'user' ? '{{ substr(auth()->user()->name, 0, 1) }}' : '⚡'"></span>
                    </div>

                    {{-- Body --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-xs font-semibold" :class="msg.role === 'user' ? 'text-gray-300' : 'text-emerald-400'"
                                  x-text="msg.role === 'user' ? 'You' : 'RyaanCMS AI'"></span>
                            <span x-show="msg.model" class="text-xs px-1.5 py-0.5 rounded bg-ryaan-500/15 text-ryaan-400 font-mono" x-text="msg.model"></span>
                            <span class="text-xs text-gray-600" x-text="msg.time"></span>
                        </div>

                        {{-- Text content --}}
                        <div class="text-sm leading-relaxed text-gray-300" x-html="formatMessage(msg.content)"></div>

                        {{-- Generated files list --}}
                        <template x-if="msg.files && msg.files.length > 0">
                            <div class="mt-3 flex flex-wrap gap-2">
                                <template x-for="file in msg.files">
                                    <button @click="openFile(file)"
                                        class="flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-lg border border-ryaan-500/30 bg-ryaan-500/10 text-ryaan-400 hover:bg-ryaan-500/20 transition-all font-mono">
                                        📄 <span x-text="file.path.split('/').pop()"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Thinking indicator --}}
            <div x-show="isThinking" x-cloak class="flex gap-3 msg-animate">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-xs flex-shrink-0">⚡</div>
                <div class="flex-1">
                    <div class="text-xs font-semibold text-emerald-400 mb-1.5">RyaanCMS AI</div>
                    <div class="flex items-center gap-2 text-xs text-gray-400 bg-[#1a1a24] border border-white/5 rounded-lg px-3 py-2 w-fit">
                        <div class="flex gap-1">
                            <div class="thinking-dot w-1.5 h-1.5 rounded-full bg-ryaan-500"></div>
                            <div class="thinking-dot w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            <div class="thinking-dot w-1.5 h-1.5 rounded-full bg-red-400"></div>
                        </div>
                        <span x-text="thinkingText"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-3 border-t border-white/5 flex-shrink-0 bg-[#111118]">
            <div class="bg-[#1a1a24] border border-white/10 rounded-xl overflow-hidden focus-within:border-ryaan-500/50 transition-colors">
                <textarea
                    x-model="prompt"
                    @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                    :disabled="isThinking"
                    rows="3"
                    placeholder="Describe what you want to build... (Enter to send, Shift+Enter for new line)"
                    class="w-full bg-transparent border-none outline-none px-4 py-3 text-sm text-gray-200 placeholder-gray-600 resize-none font-sans"
                ></textarea>
                <div class="flex items-center justify-between px-3 py-2 border-t border-white/5">
                    <div class="flex gap-2">
                        <button class="text-gray-500 hover:text-gray-300 text-sm transition-colors" title="Attach file">📎</button>
                        <button class="text-gray-500 hover:text-gray-300 text-sm transition-colors" title="Templates">🧩</button>
                        <button @click="sendQuick('Fix all bugs and errors in this project')" class="text-xs px-2 py-1 rounded border border-white/10 text-gray-500 hover:text-gray-300 hover:border-white/20 transition-all">🐛 Fix Bugs</button>
                        <button @click="sendQuick('Generate SEO meta tags and sitemap for this project')" class="text-xs px-2 py-1 rounded border border-white/10 text-gray-500 hover:text-gray-300 hover:border-white/20 transition-all">🔍 SEO</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <select x-model="model" class="bg-[#111118] border border-white/10 rounded-lg text-xs text-gray-400 px-2 py-1 outline-none cursor-pointer">
                            <option value="auto">🤖 Auto Route</option>
                            <option value="claude">Claude Sonnet</option>
                            <option value="deepseek">DeepSeek V3</option>
                        </select>
                        <button
                            @click="sendMessage()"
                            :disabled="isThinking || !prompt.trim()"
                            class="flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-semibold transition-all"
                            :class="isThinking || !prompt.trim() ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gradient-to-r from-ryaan-500 to-purple-600 text-white hover:opacity-90'"
                        >
                            <span x-show="isThinking" class="spinner">⟳</span>
                            <span x-text="isThinking ? 'Building...' : 'Send ↵'"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between mt-2 text-xs text-gray-600 px-1">
                <span>💡 Tip: Be specific — "Add a contact form with email validation and rate limiting"</span>
                <span x-text="'Tokens today: ' + tokensToday.toLocaleString()"></span>
            </div>
        </div>
    </div>

    {{-- ═══ RIGHT: PREVIEW PANEL ═══ --}}
    <div class="preview-panel" x-data="previewPanel()">

        {{-- Toolbar --}}
        <div class="flex items-center gap-2 px-3 py-2.5 border-b border-white/5 bg-[#111118] flex-shrink-0">
            <div class="flex bg-[#1a1a24] border border-white/10 rounded-lg p-0.5 gap-0.5">
                <button @click="tab = 'preview'" :class="tab === 'preview' ? 'bg-ryaan-500 text-white' : 'text-gray-500 hover:text-gray-300'" class="text-xs px-3 py-1 rounded-md transition-all">👁 Preview</button>
                <button @click="tab = 'files'"   :class="tab === 'files'   ? 'bg-ryaan-500 text-white' : 'text-gray-500 hover:text-gray-300'" class="text-xs px-3 py-1 rounded-md transition-all">📁 Files</button>
                <button @click="tab = 'db'"      :class="tab === 'db'      ? 'bg-ryaan-500 text-white' : 'text-gray-500 hover:text-gray-300'" class="text-xs px-3 py-1 rounded-md transition-all">🗄 Database</button>
            </div>
            <div class="flex-1 flex items-center gap-2 bg-[#1a1a24] border border-white/10 rounded-lg px-3 py-1.5 font-mono text-xs text-gray-500">
                <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400/50"></span>
                {{ $project->domain ?? 'localhost:8000' }}/
            </div>
            <button class="text-gray-500 hover:text-gray-300 text-sm transition-colors" title="Mobile view">📱</button>
            <button class="text-gray-500 hover:text-gray-300 text-sm transition-colors" title="Refresh">🔄</button>
            <a href="{{ route('deploy.show', $project) }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-500 text-black hover:bg-emerald-400 transition-all">
                🚀 Deploy
            </a>
        </div>

        {{-- Preview Content --}}
        <div class="flex-1 overflow-auto bg-[#0a0a0f]">

            {{-- PREVIEW TAB --}}
            <div x-show="tab === 'preview'" class="p-6 h-full">
                <div class="bg-white rounded-xl overflow-hidden shadow-2xl">
                    <div class="bg-gray-900 px-4 py-3 flex items-center justify-between">
                        <div class="font-display font-bold text-white">{{ $project->name }}</div>
                        <div class="flex gap-3 text-sm text-gray-400">
                            <span class="cursor-pointer hover:text-white">Home</span>
                            <span class="cursor-pointer hover:text-white">About</span>
                            <span class="text-ryaan-400 font-semibold cursor-pointer">Get Started →</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-950 to-indigo-950 p-16 text-center">
                        <h1 class="text-4xl font-black text-white font-display mb-4">{{ $project->name }}</h1>
                        <p class="text-gray-400 text-sm max-w-md mx-auto mb-6">{{ $project->description ?? 'Your AI-generated application is being built...' }}</p>
                        <div class="flex gap-3 justify-center">
                            <div class="bg-ryaan-500 text-white px-6 py-2.5 rounded-lg text-sm font-semibold cursor-pointer">Get Started</div>
                            <div class="border border-gray-700 text-gray-400 px-6 py-2.5 rounded-lg text-sm cursor-pointer">Learn More</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 p-8 bg-gray-50">
                        @foreach(['⚡ Fast', '🔒 Secure', '🤖 AI-Powered'] as $feat)
                        <div class="bg-white rounded-xl p-4 border border-gray-200">
                            <div class="text-lg mb-2">{{ explode(' ', $feat)[0] }}</div>
                            <div class="font-bold text-gray-900 text-sm mb-1">{{ explode(' ', $feat)[1] }}</div>
                            <div class="text-xs text-gray-500">Built with RyaanCMS AI</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- FILES TAB --}}
            <div x-show="tab === 'files'" x-cloak class="p-4">
                <div class="font-mono text-xs text-gray-400 space-y-0.5">
                    @foreach($files as $file)
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-white/5 cursor-pointer group transition-colors">
                        <span>{{ str_contains($file->path, '.blade') ? '📄' : (str_contains($file->path, '.php') ? '🐘' : '📝') }}</span>
                        <span class="text-gray-300 group-hover:text-white">{{ $file->path }}</span>
                        <span class="ml-auto opacity-0 group-hover:opacity-100 text-ryaan-400 text-xs">Edit →</span>
                    </div>
                    @endforeach
                    @if($files->isEmpty())
                    <div class="text-center py-12 text-gray-600">
                        <div class="text-3xl mb-3">📂</div>
                        <div>No files yet. Ask AI to build something!</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- DB TAB --}}
            <div x-show="tab === 'db'" x-cloak class="p-4">
                <div class="font-mono text-xs">
                    <div class="text-emerald-400 mb-3 font-semibold">📊 MySQL — {{ config('database.connections.mysql.database') }}</div>
                    <div class="bg-[#111118] border border-white/5 rounded-xl overflow-hidden">
                        <div class="grid grid-cols-3 px-4 py-2.5 bg-[#1a1a24] border-b border-white/5 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                            <span>Table</span><span>Rows</span><span>Engine</span>
                        </div>
                        @foreach(['users' => '1,204', 'projects' => '48', 'chat_messages' => '2,891', 'ai_usage_logs' => '5,420', 'marketplace_items' => '234'] as $table => $rows)
                        <div class="grid grid-cols-3 px-4 py-2.5 border-b border-white/5 text-gray-300 hover:bg-white/3 cursor-pointer transition-colors">
                            <span class="text-ryaan-400">{{ $table }}</span>
                            <span>{{ $rows }}</span>
                            <span class="text-gray-500">InnoDB</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function aiBuilder(projectId) {
    return {
        projectId,
        project: { id: projectId, name: '{{ $project->name }}' },
        messages: [],
        prompt: '',
        model: 'auto',
        isThinking: false,
        thinkingText: 'Thinking...',
        tokensToday: {{ auth()->user()->todayTokenUsage() }},

        thinkingTexts: [
            'Analyzing your request...',
            'Writing Laravel code...',
            'Generating Blade templates...',
            'Creating database schema...',
            'Optimizing for shared hosting...',
            'Adding security measures...',
        ],

        async sendMessage() {
            if (!this.prompt.trim() || this.isThinking) return;

            const userPrompt = this.prompt.trim();
            this.prompt = '';

            // Add user message
            this.messages.push({
                role: 'user',
                content: userPrompt,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            });

            this.isThinking = true;
            this.startThinkingAnimation();
            this.scrollToBottom();

            try {
                const data = await apiPost('/ai/generate', {
                    prompt: userPrompt,
                    project_id: this.projectId,
                    model: this.model,
                });

                if (data.error) {
                    this.messages.push({ role: 'assistant', content: '❌ ' + data.error, time: this.now(), model: null, files: [] });
                    showToast(data.error, 'error');
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: data.content,
                        model: data.model,
                        files: data.files || [],
                        time: this.now(),
                    });
                    this.tokensToday += (data.tokens?.input_tokens || 0) + (data.tokens?.output_tokens || 0);
                    if (data.file_count > 0) showToast(`✅ Generated ${data.file_count} files!`);
                }
            } catch (e) {
                this.messages.push({ role: 'assistant', content: '❌ Network error. Please try again.', time: this.now(), files: [] });
                showToast('Network error', 'error');
            } finally {
                this.isThinking = false;
                this.scrollToBottom();
            }
        },

        sendQuick(text) { this.prompt = text; this.sendMessage(); },

        startThinkingAnimation() {
            let i = 0;
            const interval = setInterval(() => {
                if (!this.isThinking) { clearInterval(interval); return; }
                this.thinkingText = this.thinkingTexts[i % this.thinkingTexts.length];
                i++;
            }, 1500);
        },

        formatMessage(content) {
            if (!content) return '';
            // Convert code blocks to styled HTML
            return content
                .replace(/```(\w+)?\n([\s\S]*?)```/g, (_, lang, code) => `
                    <div class="code-block mt-2">
                        <div class="code-header">
                            <span class="text-xs text-ryaan-400 font-mono">${lang || 'code'}</span>
                            <button onclick="navigator.clipboard.writeText(this.closest('.code-block').querySelector('pre').textContent)" class="text-xs text-gray-500 hover:text-white border border-white/10 rounded px-2 py-0.5 transition-colors">Copy</button>
                        </div>
                        <div class="code-content"><pre>${escapeHtml(code.trim())}</pre></div>
                    </div>`)
                .replace(/\*\*(.*?)\*\*/g, '<strong class="text-white">$1</strong>')
                .replace(/`(.*?)`/g, '<code class="bg-white/10 px-1 py-0.5 rounded text-emerald-400 font-mono text-xs">$1</code>')
                .replace(/\n/g, '<br>');
        },

        openFile(file) {
            // Open file in editor (future: code editor integration)
            console.log('Open file:', file.path);
            showToast('File saved: ' + file.path.split('/').pop());
        },

        async clearChat() {
            await apiDelete('/ai/chat/' + this.projectId);
            this.messages = [];
            showToast('Chat cleared');
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('chatMessages');
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        now() {
            return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    };
}

function previewPanel() {
    return { tab: 'preview' };
}

function escapeHtml(text) {
    return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
@endpush
