<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — RyaanCMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{display:['Syne','sans-serif'],sans:['Instrument Sans','sans-serif']}}}}</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body{font-family:'Instrument Sans',sans-serif}
        .bg-grid{background-image:radial-gradient(circle,#6c63ff15 1px,transparent 1px);background-size:28px 28px}
        @keyframes glow-pulse{0%,100%{opacity:0.4}50%{opacity:0.8}}.glow{animation:glow-pulse 3s ease infinite}
    </style>
</head>
<body class="bg-[#0a0a0f] min-h-screen flex items-center justify-center relative overflow-hidden py-8">
    <div class="absolute inset-0 bg-grid opacity-40"></div>
    <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-[#6c63ff] rounded-full blur-[130px] opacity-10 glow"></div>
    <div class="absolute bottom-1/3 left-1/4 w-72 h-72 bg-[#00d4aa] rounded-full blur-[130px] opacity-8 glow" style="animation-delay:2s"></div>

    <div class="relative z-10 w-full max-w-md px-6" x-data="registerPage()">

        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-[#6c63ff] to-[#00d4aa] rounded-xl flex items-center justify-center text-xl shadow-lg shadow-[#6c63ff]/30">⚡</div>
                <span class="font-display font-bold text-2xl text-white">Ryaan<span class="text-[#00d4aa]">CMS</span></span>
            </a>
            <h1 class="text-2xl font-display font-bold text-white mb-2">Create your account</h1>
            <p class="text-gray-500 text-sm">Free forever. No credit card required.</p>
        </div>

        <div class="bg-[#111118] border border-white/8 rounded-2xl p-8 shadow-2xl">

            <div x-show="error" x-cloak class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm">
                ❌ <span x-text="error"></span>
            </div>

            <form @submit.prevent="register()">
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Full Name</label>
                    <input x-model="name" type="text" required placeholder="Your Name"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/60 transition-colors"/>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Email Address</label>
                    <input x-model="email" type="email" required placeholder="you@example.com"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/60 transition-colors"/>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Password</label>
                    <input x-model="password" type="password" required placeholder="Min. 8 characters"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/60 transition-colors"/>
                    {{-- Strength indicator --}}
                    <div class="flex gap-1 mt-2">
                        <div class="h-1 flex-1 rounded" :class="strength >= 1 ? 'bg-red-500' : 'bg-white/10'"></div>
                        <div class="h-1 flex-1 rounded" :class="strength >= 2 ? 'bg-yellow-500' : 'bg-white/10'"></div>
                        <div class="h-1 flex-1 rounded" :class="strength >= 3 ? 'bg-emerald-500' : 'bg-white/10'"></div>
                        <div class="h-1 flex-1 rounded" :class="strength >= 4 ? 'bg-emerald-400' : 'bg-white/10'"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1" x-text="strengthLabel"></p>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Confirm Password</label>
                    <input x-model="passwordConfirm" type="password" required placeholder="Repeat password"
                        :class="passwordConfirm && password !== passwordConfirm ? 'border-red-500/50' : 'border-white/10 focus:border-[#6c63ff]/60'"
                        class="w-full bg-[#1a1a24] border rounded-xl px-4 py-3 text-sm text-gray-200 placeholder-gray-600 outline-none transition-colors"/>
                    <p x-show="passwordConfirm && password !== passwordConfirm" class="text-xs text-red-400 mt-1">Passwords don't match</p>
                </div>

                <div class="flex items-start gap-3 mb-6">
                    <input x-model="terms" type="checkbox" id="terms" class="w-4 h-4 mt-0.5 rounded bg-[#1a1a24] border-white/20 accent-[#6c63ff]" required>
                    <label for="terms" class="text-sm text-gray-400 cursor-pointer leading-relaxed">
                        I agree to the <a href="#" class="text-[#6c63ff] hover:underline">Terms of Service</a> and <a href="#" class="text-[#6c63ff] hover:underline">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" :disabled="loading || !terms || password !== passwordConfirm"
                    class="w-full py-3 rounded-xl text-sm font-semibold transition-all bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white hover:opacity-90 disabled:opacity-40 flex items-center justify-center gap-2">
                    <span x-show="loading" class="animate-spin inline-block">⟳</span>
                    <span x-text="loading ? 'Creating account...' : 'Create Free Account'"></span>
                </button>
            </form>

            {{-- Benefits --}}
            <div class="mt-6 pt-6 border-t border-white/8">
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                    <div class="flex items-center gap-1.5">✅ Free forever</div>
                    <div class="flex items-center gap-1.5">✅ No credit card</div>
                    <div class="flex items-center gap-1.5">✅ BYOK API keys</div>
                    <div class="flex items-center gap-1.5">✅ Shared hosting</div>
                </div>
            </div>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Already have an account?
            <a href="/login" class="text-[#6c63ff] hover:underline font-medium">Sign in →</a>
        </p>
    </div>

    <script>
    function registerPage() {
        return {
            name:'', email:'', password:'', passwordConfirm:'', terms:false,
            loading:false, error:'',

            get strength() {
                const p = this.password;
                let s = 0;
                if(p.length >= 8) s++;
                if(/[A-Z]/.test(p)) s++;
                if(/[0-9]/.test(p)) s++;
                if(/[^A-Za-z0-9]/.test(p)) s++;
                return s;
            },
            get strengthLabel() {
                return ['','Weak','Fair','Good','Strong'][this.strength] || '';
            },

            async register() {
                if(this.password !== this.passwordConfirm) return;
                this.loading = true; this.error = '';
                try {
                    const res = await fetch('/register', {
                        method:'POST',
                        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content||''},
                        body:JSON.stringify({name:this.name,email:this.email,password:this.password,password_confirmation:this.passwordConfirm})
                    });
                    const data = await res.json();
                    if(data.redirect) window.location.href = data.redirect;
                    else this.error = data.message || 'Registration failed.';
                } catch(e){ this.error='Network error. Please try again.'; }
                finally { this.loading = false; }
            }
        }
    }
    </script>
</body>
</html>
