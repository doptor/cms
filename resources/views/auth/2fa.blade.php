@extends('layouts.app')
@section('title', 'Two-Factor Authentication')

@section('content')
<div class="p-6 max-w-xl mx-auto" x-data="twoFactor()">

    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-[#6c63ff]/20 to-purple-500/10 border border-[#6c63ff]/20 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">🔐</div>
        <h1 class="font-display font-bold text-2xl text-white mb-2">Two-Factor Authentication</h1>
        <p class="text-gray-500 text-sm">Add an extra layer of security to your account</p>
    </div>

    {{-- NOT ENABLED --}}
    <div x-show="!enabled && !setupMode">
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="text-3xl">🛡</div>
                <div>
                    <h2 class="font-semibold text-white mb-2">Protect your account</h2>
                    <p class="text-gray-500 text-sm leading-relaxed">2FA requires a verification code from your authenticator app each time you sign in. Even if someone has your password, they can't access your account.</p>
                </div>
            </div>
        </div>

        <div class="space-y-3 mb-6">
            @foreach(['Download Google Authenticator or Authy on your phone','Scan the QR code with your authenticator app','Enter the 6-digit code to confirm setup'] as $i => $step)
            <div class="flex items-center gap-3 bg-[#111118] border border-white/5 rounded-xl p-4">
                <div class="w-7 h-7 rounded-full bg-[#6c63ff]/20 text-[#6c63ff] flex items-center justify-center text-xs font-bold flex-shrink-0">{{ $i+1 }}</div>
                <span class="text-sm text-gray-300">{{ $step }}</span>
            </div>
            @endforeach
        </div>

        <button @click="startSetup()" :disabled="loading"
            class="w-full py-3 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white font-semibold hover:opacity-90 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
            <span x-show="loading" class="animate-spin">⟳</span>
            Enable Two-Factor Authentication
        </button>
    </div>

    {{-- SETUP MODE --}}
    <div x-show="setupMode" x-cloak>
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-6 mb-4 text-center">
            <p class="text-sm text-gray-400 mb-4">Scan this QR code with your authenticator app</p>

            {{-- QR Code placeholder --}}
            <div class="inline-block p-4 bg-white rounded-xl mb-4">
                <div class="w-36 h-36 bg-gray-200 flex items-center justify-center text-gray-400 text-sm rounded">
                    QR Code Here
                </div>
            </div>

            <p class="text-xs text-gray-500 mb-2">Or enter this code manually:</p>
            <code class="text-sm font-mono bg-[#1a1a24] border border-white/10 px-4 py-2 rounded-xl text-[#6c63ff] tracking-widest" x-text="secret">XXXX XXXX XXXX XXXX</code>
        </div>

        <div class="bg-[#111118] border border-white/5 rounded-2xl p-6 mb-4">
            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Enter 6-digit code to confirm</label>
            <input x-model="code" type="text" maxlength="6" placeholder="000000"
                @input="code = code.replace(/[^0-9]/g, '')"
                class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-4 text-3xl text-white placeholder-gray-700 outline-none focus:border-[#6c63ff]/50 transition-colors font-mono tracking-[0.5em] text-center"/>
        </div>

        <div class="flex gap-3">
            <button @click="setupMode = false" class="flex-1 py-2.5 rounded-xl border border-white/10 text-sm text-gray-400 hover:border-white/20 transition-all">Cancel</button>
            <button @click="confirmSetup()" :disabled="code.length !== 6 || loading"
                class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 disabled:opacity-40 transition-all flex items-center justify-center gap-2">
                <span x-show="loading" class="animate-spin">⟳</span>
                Verify & Enable
            </button>
        </div>
    </div>

    {{-- ENABLED --}}
    <div x-show="enabled" x-cloak>
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-6 mb-6 text-center">
            <div class="text-4xl mb-3">✅</div>
            <h2 class="font-semibold text-emerald-400 mb-1">2FA is Active</h2>
            <p class="text-gray-500 text-sm">Your account is protected with two-factor authentication</p>
        </div>

        {{-- Recovery Codes --}}
        <div class="bg-[#111118] border border-amber-500/20 rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-amber-400">⚠️</span>
                <h3 class="font-semibold text-white text-sm">Recovery Codes</h3>
            </div>
            <p class="text-xs text-gray-500 mb-4">Save these codes somewhere safe. Use them to access your account if you lose your authenticator device.</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach(['a1b2-c3d4','e5f6-g7h8','i9j0-k1l2','m3n4-o5p6','q7r8-s9t0','u1v2-w3x4'] as $code)
                <code class="bg-[#1a1a24] border border-white/8 rounded-lg px-3 py-2 text-xs font-mono text-gray-300 text-center">{{ $code }}</code>
                @endforeach
            </div>
            <button class="mt-4 w-full py-2 rounded-xl border border-white/10 text-xs text-gray-400 hover:border-white/20 transition-all">📋 Copy All Codes</button>
        </div>

        <button @click="disable()" class="w-full py-2.5 rounded-xl border border-red-500/30 text-red-400 text-sm hover:bg-red-500/10 transition-all">
            Disable Two-Factor Authentication
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function twoFactor() {
    return {
        enabled: {{ auth()->user()->two_factor_enabled ? 'true' : 'false' }},
        setupMode: false, loading: false,
        code: '', secret: 'JBSW Y3DP EHPK 3PXP',

        async startSetup() {
            this.loading = true;
            await new Promise(r => setTimeout(r, 800));
            this.setupMode = true;
            this.loading = false;
        },

        async confirmSetup() {
            if (this.code.length !== 6) return;
            this.loading = true;
            try {
                const data = await apiPost('/settings/2fa/enable', { code: this.code });
                if (data.success) { this.enabled = true; this.setupMode = false; showToast('✅ 2FA enabled successfully!'); }
                else showToast(data.message || 'Invalid code', 'error');
            } catch { showToast('Error enabling 2FA', 'error'); }
            finally { this.loading = false; this.code = ''; }
        },

        async disable() {
            if (!confirm('Disable two-factor authentication? This will make your account less secure.')) return;
            const data = await apiPost('/settings/2fa/disable', {});
            if (data.success) { this.enabled = false; showToast('2FA disabled'); }
        }
    }
}
</script>
@endpush
