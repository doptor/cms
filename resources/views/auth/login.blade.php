<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — RyaanCMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Instrument+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { display: ['Syne','sans-serif'], mono: ['DM Mono','monospace'], sans: ['Instrument Sans','sans-serif'] } } } }</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .bg-grid { background-image: radial-gradient(circle, #6c63ff15 1px, transparent 1px); background-size: 28px 28px; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .float { animation: float 5s ease-in-out infinite; }
        @keyframes glow-pulse { 0%,100%{opacity:0.4} 50%{opacity:0.8} }
        .glow { animation: glow-pulse 3s ease infinite; }
    </style>
</head>
<body class="bg-[#0a0a0f] min-h-screen flex items-center justify-center relative overflow-hidden">

    {{-- Background effects --}}
    <div class="absolute inset-0 bg-grid opacity-40"></div>
    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-[#6c63ff] rounded-full blur-[120px] opacity-10 glow"></div>
    <div class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-[#00d4aa] rounded-full blur-[120px] opacity-8 glow" style="animation-delay:1.5s"></div>

    <div class="relative z-10 w-full max-w-md px-6" x-data="loginPage()">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-[#6c63ff] to-[#00d4aa] rounded-xl flex items-center justify-center text-xl shadow-lg shadow-[#6c63ff]/30">⚡</div>
                <span class="font-display font-bold text-2xl text-white">Ryaan<span class="text-[#00d4aa]">CMS</span></span>
            </a>
            <h1 class="text-2xl font-display font-bold text-white mb-2">Welcome back</h1>
            <p class="text-gray-500 text-sm">Sign in to your workspace</p>
        </div>

        {{-- Card --}}
        <div class="bg-[#111118] border border-white/8 rounded-2xl p-8 shadow-2xl">

            {{-- Error --}}
            <div x-show="error" x-cloak class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm flex items-center gap-2">
                ❌ <span x-text="error"></span>
            </div>

            <form @submit.prevent="login()">
                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Email Address</label>
                    <input x-model="email" type="email" required autocomplete="email"
                        placeholder="you@example.com"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/60 transition-colors" />
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Password</label>
                        <a href="/forgot-password" class="text-xs text-[#6c63ff] hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <input x-model="password" :type="showPass ? 'text' : 'password'" required autocomplete="current-password"
                            placeholder="••••••••••"
                            class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/60 transition-colors pr-12" />
                        <button type="button" @click="showPass = !showPass" class="absolute right-3 top-3 text-gray-500 hover:text-gray-300 transition-colors text-sm">
                            <span x-text="showPass ? '🙈' : '👁'"></span>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <div class="flex items-center gap-2 mb-6">
                    <input x-model="remember" type="checkbox" id="remember" class="w-4 h-4 rounded bg-[#1a1a24] border-white/20 accent-[#6c63ff]">
                    <label for="remember" class="text-sm text-gray-400 cursor-pointer">Remember me for 30 days</label>
                </div>

                {{-- Submit --}}
                <button type="submit" :disabled="loading"
                    class="w-full py-3 rounded-xl text-sm font-semibold transition-all bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white hover:opacity-90 disabled:opacity-50 flex items-center justify-center gap-2">
                    <span x-show="loading" class="inline-block animate-spin">⟳</span>
                    <span x-text="loading ? 'Signing in...' : 'Sign In'"></span>
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-white/8"></div>
                <span class="text-xs text-gray-600">or</span>
                <div class="flex-1 h-px bg-white/8"></div>
            </div>

            {{-- GitHub OAuth placeholder --}}
            <button class="w-full py-3 rounded-xl border border-white/10 text-sm text-gray-400 hover:border-white/20 hover:text-gray-300 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.21 11.38.6.11.82-.26.82-.58v-2.03c-3.34.73-4.04-1.61-4.04-1.61-.54-1.38-1.33-1.75-1.33-1.75-1.09-.74.08-.73.08-.73 1.2.08 1.84 1.24 1.84 1.24 1.07 1.83 2.8 1.3 3.49 1 .11-.78.42-1.3.76-1.6-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.12-.3-.54-1.52.12-3.18 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 013.01-.4c1.02 0 2.05.14 3.01.4 2.29-1.55 3.3-1.23 3.3-1.23.66 1.66.24 2.88.12 3.18.77.84 1.24 1.91 1.24 3.22 0 4.61-2.81 5.63-5.48 5.92.43.37.82 1.1.82 2.22v3.29c0 .32.21.7.83.58C20.57 21.8 24 17.3 24 12c0-6.63-5.37-12-12-12z"/></svg>
                Continue with GitHub
            </button>
        </div>

        {{-- Register link --}}
        <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account?
            <a href="/register" class="text-[#6c63ff] hover:underline font-medium">Create one free →</a>
        </p>

        <p class="text-center text-xs text-gray-700 mt-4">
            Free & Open Source · <a href="https://github.com/ryaancms/ryaancms" class="hover:text-gray-500">⭐ GitHub</a>
        </p>
    </div>

    <script>
    function loginPage() {
        return {
            email: '', password: '', remember: false,
            loading: false, showPass: false, error: '',

            async login() {
                this.loading = true; this.error = '';
                try {
                    const res = await fetch('/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
                        body: JSON.stringify({ email: this.email, password: this.password, remember: this.remember })
                    });
                    const data = await res.json();
                    if (data.redirect) window.location.href = data.redirect;
                    else this.error = data.message || 'Invalid credentials.';
                } catch(e) { this.error = 'Network error. Please try again.'; }
                finally { this.loading = false; }
            }
        }
    }
    </script>
</body>
</html>
