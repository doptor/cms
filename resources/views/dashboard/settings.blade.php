{{-- resources/views/dashboard/settings.blade.php --}}
@extends('layouts.app')
@section('title', 'Settings — API Keys')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-6" x-data="settingsPage()">
    <h1 class="font-display font-bold text-2xl text-white mb-1">Settings</h1>
    <p class="text-gray-500 text-sm mb-8">Manage your AI API keys and preferences. Keys are encrypted with AES-256.</p>

    {{-- AI API KEYS CARD --}}
    <div class="bg-[#111118] border border-white/5 rounded-2xl p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-9 h-9 rounded-xl bg-ryaan-500/20 flex items-center justify-center text-lg">🔑</div>
            <div>
                <h2 class="font-semibold text-white text-sm">AI Engine API Keys</h2>
                <p class="text-xs text-gray-500">Your keys never leave your server. Encrypted at rest.</p>
            </div>
        </div>

        {{-- AI Selection --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div @click="selectedAi = 'claude'" :class="selectedAi === 'claude' ? 'border-ryaan-500 bg-ryaan-500/8' : 'border-white/10 hover:border-white/20'"
                class="border-2 rounded-xl p-4 cursor-pointer transition-all text-center">
                <div class="text-2xl mb-2">🤖</div>
                <div class="font-bold text-sm text-white mb-1">Claude Sonnet</div>
                <div class="text-xs text-gray-500">Best quality & reasoning</div>
                @if($has_claude)
                    <div class="mt-2 text-xs text-emerald-400">✅ Active</div>
                @endif
            </div>
            <div @click="selectedAi = 'deepseek'" :class="selectedAi === 'deepseek' ? 'border-emerald-500 bg-emerald-500/8' : 'border-white/10 hover:border-white/20'"
                class="border-2 rounded-xl p-4 cursor-pointer transition-all text-center">
                <div class="text-2xl mb-2">⚡</div>
                <div class="font-bold text-sm text-white mb-1">DeepSeek V3</div>
                <div class="text-xs text-gray-500">Most economic option</div>
                @if($has_deepseek)
                    <div class="mt-2 text-xs text-emerald-400">✅ Active</div>
                @endif
            </div>
        </div>

        {{-- Claude Key --}}
        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Anthropic API Key (Claude)</label>
            <div class="flex gap-2">
                <input x-model="claudeKey" type="password" placeholder="sk-ant-api03-..."
                    class="flex-1 bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm font-mono text-gray-300 outline-none focus:border-ryaan-500/50 transition-colors" />
                @if($has_claude)
                <button @click="removeKey('claude')" class="px-3 py-2 text-xs rounded-xl border border-red-500/30 text-red-400 hover:bg-red-500/10 transition-colors">Remove</button>
                @endif
            </div>
            <p class="text-xs text-gray-600 mt-1.5">Get your key at <a href="https://console.anthropic.com" target="_blank" class="text-ryaan-400 hover:underline">console.anthropic.com</a>
                @if($claude_key_masked) · Current: <span class="font-mono text-gray-400">{{ $claude_key_masked }}</span> @endif
            </p>
        </div>

        {{-- DeepSeek Key --}}
        <div class="mb-6">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">DeepSeek API Key</label>
            <div class="flex gap-2">
                <input x-model="deepseekKey" type="password" placeholder="sk-..."
                    class="flex-1 bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm font-mono text-gray-300 outline-none focus:border-emerald-500/50 transition-colors" />
                @if($has_deepseek)
                <button @click="removeKey('deepseek')" class="px-3 py-2 text-xs rounded-xl border border-red-500/30 text-red-400 hover:bg-red-500/10 transition-colors">Remove</button>
                @endif
            </div>
            <p class="text-xs text-gray-600 mt-1.5">Get your key at <a href="https://platform.deepseek.com" target="_blank" class="text-emerald-400 hover:underline">platform.deepseek.com</a>
                @if($deepseek_key_masked) · Current: <span class="font-mono text-gray-400">{{ $deepseek_key_masked }}</span> @endif
            </p>
        </div>

        {{-- Routing Mode --}}
        <div class="mb-6">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">AI Routing Mode</label>
            <select x-model="routingMode" class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-300 outline-none cursor-pointer">
                <option value="auto">🤖 Auto — Smart routing by complexity (Recommended)</option>
                <option value="claude">Claude Only — Best quality</option>
                <option value="deepseek">DeepSeek Only — Most economic</option>
            </select>
        </div>

        <button @click="saveKeys()" :disabled="saving"
            class="w-full py-2.5 rounded-xl bg-gradient-to-r from-ryaan-500 to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all disabled:opacity-50">
            <span x-text="saving ? '⏳ Verifying & Saving...' : '💾 Save & Verify API Keys'"></span>
        </button>
    </div>

    {{-- USAGE STATS --}}
    <div class="bg-[#111118] border border-white/5 rounded-2xl p-6 mb-6">
        <h2 class="font-semibold text-white text-sm mb-4">📊 AI Usage Statistics</h2>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-[#1a1a24] rounded-xl p-4">
                <div class="text-2xl font-bold text-white font-mono">{{ number_format($today_tokens) }}</div>
                <div class="text-xs text-gray-500 mt-1">Tokens today</div>
            </div>
            <div class="bg-[#1a1a24] rounded-xl p-4">
                <div class="text-2xl font-bold text-white font-mono">{{ number_format($month_tokens) }}</div>
                <div class="text-xs text-gray-500 mt-1">Tokens this month</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function settingsPage() {
    return {
        selectedAi: 'claude',
        claudeKey: '',
        deepseekKey: '',
        routingMode: '{{ $ai_routing_mode }}',
        saving: false,

        async saveKeys() {
            this.saving = true;
            try {
                const data = await apiPost('/settings/api-keys', {
                    claude_api_key:   this.claudeKey || undefined,
                    deepseek_api_key: this.deepseekKey || undefined,
                    ai_routing_mode:  this.routingMode,
                });
                if (data.success) {
                    showToast(data.message);
                    this.claudeKey = '';
                    this.deepseekKey = '';
                } else {
                    showToast(data.errors?.join(', ') || 'Validation failed', 'error');
                }
            } catch(e) {
                showToast('Error saving keys', 'error');
            } finally {
                this.saving = false;
            }
        },

        async removeKey(provider) {
            if (!confirm(`Remove ${provider} API key?`)) return;
            const data = await apiDelete(`/settings/api-keys/${provider}`);
            if (data.success) { showToast(data.message); location.reload(); }
        }
    };
}
</script>
@endpush
