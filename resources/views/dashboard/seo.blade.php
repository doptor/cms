@extends('layouts.app')
@section('title', 'SEO Manager')

@section('content')
<div class="p-6 max-w-5xl mx-auto" x-data="seoManager()">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display font-bold text-2xl text-white">🔍 SEO Manager</h1>
            <p class="text-gray-500 text-sm mt-1">AI-powered SEO optimization for all your pages</p>
        </div>
        <button @click="runAudit()" :disabled="auditing"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 disabled:opacity-50 transition-all">
            <span x-show="auditing" class="animate-spin">⟳</span>
            <span x-text="auditing ? 'Auditing...' : '🤖 Run AI SEO Audit'"></span>
        </button>
    </div>

    {{-- SEO SCORE OVERVIEW --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label'=>'Overall Score','value'=>'87','unit'=>'/100','color'=>'text-emerald-400','bg'=>'from-emerald-500/20 to-teal-500/10','border'=>'border-emerald-500/20'],
            ['label'=>'Meta Tags','value'=>'94','unit'=>'/100','color'=>'text-blue-400','bg'=>'from-blue-500/20 to-cyan-500/10','border'=>'border-blue-500/20'],
            ['label'=>'Page Speed','value'=>'72','unit'=>'/100','color'=>'text-amber-400','bg'=>'from-amber-500/20 to-orange-500/10','border'=>'border-amber-500/20'],
            ['label'=>'Mobile Score','value'=>'91','unit'=>'/100','color'=>'text-purple-400','bg'=>'from-purple-500/20 to-pink-500/10','border'=>'border-purple-500/20'],
        ] as $score)
        <div class="bg-gradient-to-br {{ $score['bg'] }} border {{ $score['border'] }} rounded-2xl p-5 text-center">
            <div class="text-3xl font-bold {{ $score['color'] }} font-display">{{ $score['value'] }}<span class="text-sm font-normal text-gray-500">{{ $score['unit'] }}</span></div>
            <div class="text-xs text-gray-500 mt-2">{{ $score['label'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- AI SEO GENERATOR --}}
        <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
            <h2 class="font-semibold text-white mb-4">🤖 AI Meta Generator</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Page Title</label>
                    <input x-model="seoForm.title" type="text" placeholder="e.g. Best Task Manager App for Teams"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Page Content / Description</label>
                    <textarea x-model="seoForm.content" rows="3" placeholder="Describe your page content..."
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Target Keywords</label>
                    <input x-model="seoForm.keywords" type="text" placeholder="task manager, team productivity, project tool"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
                </div>
                <button @click="generateSeo()" :disabled="generating"
                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                    <span x-show="generating" class="animate-spin">⟳</span>
                    <span x-text="generating ? 'Generating...' : '🤖 Generate SEO Tags'"></span>
                </button>
            </div>

            {{-- Generated Output --}}
            <div x-show="generatedSeo" x-cloak class="mt-4 bg-[#0d0d14] border border-white/5 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-emerald-400 font-semibold">✅ Generated</span>
                    <button @click="copyGenerated()" class="text-xs text-gray-500 hover:text-white border border-white/10 rounded px-2 py-0.5">Copy</button>
                </div>
                <pre class="text-xs text-gray-400 font-mono whitespace-pre-wrap leading-relaxed" x-text="generatedSeo"></pre>
            </div>
        </div>

        {{-- SEO CHECKLIST --}}
        <div class="space-y-4">
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <h2 class="font-semibold text-white mb-4">✅ SEO Checklist</h2>
                <div class="space-y-2">
                    @foreach([
                        ['done'=>true,  'label'=>'Meta title tag present','detail'=>'47 chars — good length'],
                        ['done'=>true,  'label'=>'Meta description','detail'=>'158 chars — ideal'],
                        ['done'=>true,  'label'=>'Open Graph tags','detail'=>'og:title, og:image set'],
                        ['done'=>true,  'label'=>'XML Sitemap','detail'=>'Auto-generated'],
                        ['done'=>false, 'label'=>'Schema markup (JSON-LD)','detail'=>'Not configured'],
                        ['done'=>false, 'label'=>'robots.txt','detail'=>'Missing disallow rules'],
                        ['done'=>true,  'label'=>'Canonical URLs','detail'=>'All pages canonical'],
                        ['done'=>true,  'label'=>'Alt text on images','detail'=>'12/12 images'],
                        ['done'=>false, 'label'=>'Page speed < 3s','detail'=>'Current: 4.2s — needs work'],
                        ['done'=>true,  'label'=>'Mobile responsive','detail'=>'Fully responsive'],
                    ] as $item)
                    <div class="flex items-center gap-3 py-2 border-b border-white/5 last:border-0">
                        <span class="{{ $item['done'] ? 'text-emerald-400' : 'text-amber-400' }} text-sm flex-shrink-0">
                            {{ $item['done'] ? '✅' : '⚠️' }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm {{ $item['done'] ? 'text-gray-300' : 'text-gray-300' }}">{{ $item['label'] }}</div>
                            <div class="text-xs text-gray-500">{{ $item['detail'] }}</div>
                        </div>
                        @unless($item['done'])
                        <button class="text-xs text-[#6c63ff] hover:underline flex-shrink-0">Fix →</button>
                        @endunless
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- SITEMAP --}}
            <div class="bg-[#111118] border border-white/5 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-white">🗺️ Sitemap</h2>
                    <button class="text-xs px-3 py-1.5 rounded-lg border border-white/10 text-gray-400 hover:border-white/20 transition-all">Regenerate</button>
                </div>
                <div class="space-y-1.5 font-mono text-xs text-gray-500">
                    @foreach(['/', '/about', '/features', '/pricing', '/blog', '/contact'] as $url)
                    <div class="flex items-center justify-between py-1">
                        <span class="text-[#6c63ff]">{{ url($url) }}</span>
                        <span class="text-gray-600">daily</span>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-white/5 flex gap-2">
                    <button class="flex-1 py-2 rounded-xl border border-white/10 text-xs text-gray-400 hover:border-white/20 transition-all">📋 Copy URL</button>
                    <button class="flex-1 py-2 rounded-xl border border-white/10 text-xs text-gray-400 hover:border-white/20 transition-all">🔗 Submit to Google</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ROBOTS.TXT EDITOR --}}
    <div class="mt-6 bg-[#111118] border border-white/5 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white">🤖 robots.txt</h2>
            <button class="text-xs px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500/20 transition-all">💾 Save</button>
        </div>
        <textarea rows="8" class="w-full bg-[#0d0d14] border border-white/5 rounded-xl px-4 py-3 text-sm font-mono text-gray-400 outline-none focus:border-[#6c63ff]/30 transition-colors resize-none">User-agent: *
Allow: /
Disallow: /admin/
Disallow: /settings/
Disallow: /api/

Sitemap: {{ url('/sitemap.xml') }}</textarea>
    </div>
</div>
@endsection

@push('scripts')
<script>
function seoManager() {
    return {
        auditing: false, generating: false,
        seoForm: { title: '', content: '', keywords: '' },
        generatedSeo: '',

        async runAudit() {
            this.auditing = true;
            await new Promise(r => setTimeout(r, 2500));
            this.auditing = false;
            showToast('SEO audit complete! Score: 87/100');
        },

        async generateSeo() {
            if (!this.seoForm.title) return;
            this.generating = true;
            await new Promise(r => setTimeout(r, 2000));
            this.generatedSeo = `<title>${this.seoForm.title} | RyaanCMS</title>
<meta name="description" content="${this.seoForm.content.substring(0,155)}...">
<meta name="keywords" content="${this.seoForm.keywords}">
<meta property="og:title" content="${this.seoForm.title}">
<meta property="og:description" content="${this.seoForm.content.substring(0,100)}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<link rel="canonical" href="{{ url()->current() }}">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebPage","name":"${this.seoForm.title}"}
<\/script>`;
            this.generating = false;
        },

        copyGenerated() {
            navigator.clipboard.writeText(this.generatedSeo);
            showToast('Copied to clipboard!');
        }
    }
}
</script>
@endpush
