<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RyaanCMS — Free AI-Powered Laravel CMS</title>
    <meta name="description" content="Build complex applications with a simple prompt. Free, open-source Laravel CMS that runs on shared hosting.">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Instrument+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{display:['Syne','sans-serif'],mono:['DM Mono','monospace'],sans:['Instrument Sans','sans-serif']}}}}</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body{font-family:'Instrument Sans',sans-serif}
        .bg-grid{background-image:radial-gradient(circle,#6c63ff18 1px,transparent 1px);background-size:32px 32px}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-16px)}}.float{animation:float 6s ease-in-out infinite}
        @keyframes glow{0%,100%{opacity:0.3}50%{opacity:0.7}}.glow{animation:glow 4s ease infinite}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}.fade-up{animation:fadeUp 0.7s ease forwards}
        .delay-1{animation-delay:0.1s}.delay-2{animation-delay:0.2s}.delay-3{animation-delay:0.3s}.delay-4{animation-delay:0.4s}
        @keyframes typing{from{width:0}to{width:100%}}.typing{overflow:hidden;white-space:nowrap;animation:typing 2s steps(40,end) 1s forwards;width:0}
    </style>
</head>
<body class="bg-[#0a0a0f] text-gray-100 overflow-x-hidden">

    {{-- HEADER --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#0a0a0f]/80 backdrop-blur-xl border-b border-white/5">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-gradient-to-br from-[#6c63ff] to-[#00d4aa] rounded-lg flex items-center justify-center text-sm shadow-lg shadow-[#6c63ff]/30">⚡</div>
                <span class="font-display font-bold text-xl text-white">Ryaan<span class="text-[#00d4aa]">CMS</span></span>
            </div>
            <nav class="hidden md:flex items-center gap-6 text-sm text-gray-400">
                <a href="#features" class="hover:text-white transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-white transition-colors">How it works</a>
                <a href="{{ route('marketplace.index') }}" class="hover:text-white transition-colors">Marketplace</a>
                <a href="https://github.com" class="hover:text-white transition-colors">GitHub</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Login</a>
                <a href="{{ route('register') }}" class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all shadow-lg shadow-[#6c63ff]/20">
                    Get Started Free →
                </a>
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="relative pt-32 pb-24 px-6 overflow-hidden">
        <div class="absolute inset-0 bg-grid opacity-50"></div>
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-[#6c63ff] rounded-full blur-[150px] opacity-10 glow"></div>
        <div class="absolute top-1/3 right-1/4 w-80 h-80 bg-[#00d4aa] rounded-full blur-[130px] opacity-8 glow" style="animation-delay:2s"></div>

        <div class="relative max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#6c63ff]/30 bg-[#6c63ff]/8 text-sm text-[#6c63ff] mb-8 fade-up">
                <span class="w-2 h-2 rounded-full bg-[#6c63ff] animate-pulse"></span>
                Free & Open Source · Laravel 11 · Shared Hosting Ready
            </div>

            <h1 class="font-display font-black text-5xl md:text-7xl text-white leading-tight mb-6 fade-up delay-1">
                Build Apps with<br>
                <span class="bg-gradient-to-r from-[#6c63ff] to-[#00d4aa] bg-clip-text text-transparent">Just a Prompt</span>
            </h1>

            <p class="text-gray-400 text-xl max-w-2xl mx-auto mb-10 leading-relaxed fade-up delay-2">
                RyaanCMS is the world's first AI-powered Laravel CMS that runs on shared hosting. Describe what you want — Claude & DeepSeek build it for you.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16 fade-up delay-3">
                <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white font-semibold hover:opacity-90 transition-all shadow-2xl shadow-[#6c63ff]/25 text-lg">
                    Start Building Free →
                </a>
                <a href="https://github.com" class="px-8 py-4 rounded-xl border border-white/15 text-gray-300 font-semibold hover:border-white/30 hover:text-white transition-all text-lg flex items-center gap-2 justify-center">
                    ⭐ Star on GitHub
                </a>
            </div>

            {{-- DEMO PROMPT --}}
            <div class="max-w-2xl mx-auto bg-[#111118] border border-white/8 rounded-2xl overflow-hidden shadow-2xl fade-up delay-4">
                <div class="flex items-center gap-2 px-4 py-3 border-b border-white/5 bg-[#1a1a24]">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500/60"></div>
                    <span class="ml-2 text-xs text-gray-600 font-mono">RyaanCMS AI Builder</span>
                    <span class="ml-auto text-xs px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/25">● Live</span>
                </div>
                <div class="p-6">
                    <div class="flex gap-3 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-[#6c63ff] to-purple-600 flex items-center justify-center text-xs flex-shrink-0">U</div>
                        <div class="bg-[#1a1a24] rounded-xl px-4 py-3 text-sm text-gray-300 text-left">
                            Build me a complete SaaS landing page with hero, features, pricing (3 plans), testimonials, and dark theme
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-sm flex-shrink-0">⚡</div>
                        <div class="flex-1 text-left">
                            <div class="text-xs text-emerald-400 mb-2 font-semibold">RyaanCMS AI · Claude Sonnet</div>
                            <div class="text-xs text-gray-400 space-y-1">
                                <div class="flex items-center gap-2"><span class="text-emerald-400">✅</span> Generated <code class="bg-white/10 px-1 rounded">landing.blade.php</code> (847 lines)</div>
                                <div class="flex items-center gap-2"><span class="text-emerald-400">✅</span> Created pricing table with toggle</div>
                                <div class="flex items-center gap-2"><span class="text-emerald-400">✅</span> Added SEO meta tags & Open Graph</div>
                                <div class="flex items-center gap-2"><span class="text-emerald-400">✅</span> Ready to deploy to shared hosting</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES --}}
    <section id="features" class="py-24 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="font-display font-bold text-4xl text-white mb-4">Everything you need to build</h2>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">One platform. AI developer, shared hosting deploy, marketplace — all free.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['icon'=>'🤖','title'=>'AI Full Stack Developer','desc'=>'Claude + DeepSeek write complete Laravel code, database schemas, Blade UI, and fix bugs — just describe what you want.'],
                    ['icon'=>'🌐','title'=>'Shared Hosting Ready','desc'=>'Runs on any cPanel hosting with MySQL and PHP 8.2. No VPS, no Docker, no complex setup required.'],
                    ['icon'=>'🔑','title'=>'Bring Your Own API Key','desc'=>'Use your own Claude or DeepSeek keys. Your keys, your cost, your control. Platform costs nearly $0.'],
                    ['icon'=>'🚀','title'=>'One-Click FTP Deploy','desc'=>'Auto-deploys your generated Laravel app directly to shared hosting via FTP. Configures .env, runs migrations.'],
                    ['icon'=>'🛒','title'=>'Central Marketplace','desc'=>'Buy and sell Laravel apps, templates, and plugins. Keep 70% of every sale. Build once, earn forever.'],
                    ['icon'=>'🔒','title'=>'High Security','desc'=>'AES-256 encrypted API keys, CSRF protection, role-based access, 2FA, rate limiting, and SQL injection prevention.'],
                    ['icon'=>'🔍','title'=>'SEO Built-In','desc'=>'AI-generated meta tags, Open Graph, JSON-LD schema, XML sitemap, robots.txt — all auto-configured.'],
                    ['icon'=>'📊','title'=>'Built-In Analytics','desc'=>'Page view tracking, visitor stats, AI token usage monitoring — no Google Analytics needed.'],
                    ['icon'=>'🆓','title'=>'Free & Open Source','desc'=>'MIT licensed. Fork it, extend it, deploy it anywhere. No hidden fees, no vendor lock-in. Ever.'],
                ] as $feat)
                <div class="bg-[#111118] border border-white/5 rounded-2xl p-6 hover:border-[#6c63ff]/25 transition-all group">
                    <div class="text-3xl mb-4">{{ $feat['icon'] }}</div>
                    <h3 class="font-display font-bold text-white text-lg mb-2">{{ $feat['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $feat['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-24 px-6">
        <div class="max-w-3xl mx-auto text-center">
            <div class="bg-gradient-to-br from-[#6c63ff]/15 to-[#00d4aa]/8 border border-[#6c63ff]/20 rounded-3xl p-12">
                <div class="text-5xl mb-6 float">⚡</div>
                <h2 class="font-display font-black text-4xl text-white mb-4">Start building for free</h2>
                <p class="text-gray-400 mb-8 text-lg">No credit card. No hosting required to start. Just create an account and describe your app.</p>
                <a href="{{ route('register') }}"
                    class="inline-flex items-center gap-2 px-10 py-4 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-lg font-bold hover:opacity-90 transition-all shadow-2xl shadow-[#6c63ff]/25">
                    Get Started Free →
                </a>
                <p class="text-xs text-gray-600 mt-6">Free forever · Open source · MIT License</p>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="border-t border-white/5 py-8 px-6">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-gradient-to-br from-[#6c63ff] to-[#00d4aa] rounded-md flex items-center justify-center text-xs">⚡</div>
                <span class="font-display font-bold text-white">RyaanCMS</span>
                <span class="text-gray-600 text-sm">v1.0.0 · MIT License</span>
            </div>
            <div class="flex items-center gap-6 text-sm text-gray-500">
                <a href="#" class="hover:text-white transition-colors">Documentation</a>
                <a href="#" class="hover:text-white transition-colors">GitHub</a>
                <a href="{{ route('marketplace.index') }}" class="hover:text-white transition-colors">Marketplace</a>
                <a href="#" class="hover:text-white transition-colors">Privacy</a>
            </div>
        </div>
    </footer>

</body>
</html>
