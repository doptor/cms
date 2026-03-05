@extends('layouts.app')
@section('title', 'Page Builder')

@section('content')
<div class="flex h-[calc(100vh-112px)]" x-data="pageBuilder()">

    {{-- LEFT: BLOCK PALETTE --}}
    <div class="w-64 bg-[#111118] border-r border-white/5 flex flex-col overflow-hidden flex-shrink-0">
        <div class="px-4 py-3 border-b border-white/5">
            <h2 class="font-semibold text-white text-sm mb-2">🧩 Blocks</h2>
            <div class="flex bg-[#1a1a24] border border-white/8 rounded-lg p-0.5">
                <button @click="paletteTab='layout'"  :class="paletteTab==='layout'  ? 'bg-[#6c63ff] text-white' : 'text-gray-500'" class="flex-1 text-xs py-1.5 rounded-md transition-all">Layout</button>
                <button @click="paletteTab='content'" :class="paletteTab==='content' ? 'bg-[#6c63ff] text-white' : 'text-gray-500'" class="flex-1 text-xs py-1.5 rounded-md transition-all">Content</button>
                <button @click="paletteTab='forms'"   :class="paletteTab==='forms'   ? 'bg-[#6c63ff] text-white' : 'text-gray-500'" class="flex-1 text-xs py-1.5 rounded-md transition-all">Forms</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-1.5">

            {{-- Layout Blocks --}}
            <div x-show="paletteTab==='layout'" class="space-y-1.5">
                @foreach([
                    ['hero',       '🏠', 'Hero Section',    'Full-width hero with headline & CTA'],
                    ['features',   '✨', 'Features Grid',   '3-column feature cards'],
                    ['pricing',    '💰', 'Pricing Table',   'Plans with toggle & CTA'],
                    ['testimonials','⭐','Testimonials',    'Reviews carousel'],
                    ['cta',        '📣', 'Call to Action',  'Full-width CTA banner'],
                    ['stats',      '📊', 'Stats Counter',   'Animated number counters'],
                    ['team',       '👥', 'Team Section',    'Team member cards'],
                    ['faq',        '❓', 'FAQ Accordion',   'Expandable Q&A section'],
                    ['footer',     '🔗', 'Footer',          'Links, social, copyright'],
                ] as [$type,$icon,$label,$desc])
                <div draggable="true"
                    @dragstart="dragBlock('{{ $type }}')"
                    @click="addBlock('{{ $type }}')"
                    class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#1a1a24] hover:border-[#6c63ff]/40 hover:bg-[#6c63ff]/5 cursor-grab active:cursor-grabbing transition-all group">
                    <span class="text-lg flex-shrink-0">{{ $icon }}</span>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-gray-300 group-hover:text-white">{{ $label }}</div>
                        <div class="text-xs text-gray-600 truncate">{{ $desc }}</div>
                    </div>
                    <span class="ml-auto text-gray-700 group-hover:text-[#6c63ff] text-sm flex-shrink-0">＋</span>
                </div>
                @endforeach
            </div>

            {{-- Content Blocks --}}
            <div x-show="paletteTab==='content'" x-cloak class="space-y-1.5">
                @foreach([
                    ['heading',  '📝','Heading',     'H1-H6 text block'],
                    ['paragraph','¶', 'Paragraph',   'Rich text paragraph'],
                    ['image',    '🖼','Image',        'Responsive image'],
                    ['video',    '▶️','Video Embed',  'YouTube/Vimeo embed'],
                    ['button',   '🔘','Button',       'CTA button'],
                    ['divider',  '—', 'Divider',      'Horizontal rule'],
                    ['spacer',   '↕', 'Spacer',       'Vertical space'],
                    ['columns',  '⊞', '2 Columns',    'Two-column layout'],
                    ['card',     '🃏','Card',          'Content card'],
                    ['badge',    '🏷','Badge',         'Label/tag badge'],
                ] as [$type,$icon,$label,$desc])
                <div draggable="true" @dragstart="dragBlock('{{ $type }}')" @click="addBlock('{{ $type }}')"
                    class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#1a1a24] hover:border-[#6c63ff]/40 hover:bg-[#6c63ff]/5 cursor-grab transition-all group">
                    <span class="text-lg flex-shrink-0">{{ $icon }}</span>
                    <div><div class="text-xs font-semibold text-gray-300 group-hover:text-white">{{ $label }}</div><div class="text-xs text-gray-600">{{ $desc }}</div></div>
                    <span class="ml-auto text-gray-700 group-hover:text-[#6c63ff] text-sm">＋</span>
                </div>
                @endforeach
            </div>

            {{-- Form Blocks --}}
            <div x-show="paletteTab==='forms'" x-cloak class="space-y-1.5">
                @foreach([
                    ['contact_form','📧','Contact Form',  'Name, email, message'],
                    ['newsletter',  '📮','Newsletter',    'Email subscribe form'],
                    ['text_input',  '⌨️','Text Input',    'Single line input'],
                    ['textarea',    '📄','Textarea',      'Multi-line input'],
                    ['select',      '▼', 'Dropdown',      'Select menu'],
                    ['checkbox',    '☑', 'Checkbox',      'Toggle option'],
                    ['submit_btn',  '🚀','Submit Button', 'Form submit'],
                ] as [$type,$icon,$label,$desc])
                <div draggable="true" @dragstart="dragBlock('{{ $type }}')" @click="addBlock('{{ $type }}')"
                    class="flex items-center gap-3 p-3 rounded-xl border border-white/5 bg-[#1a1a24] hover:border-[#6c63ff]/40 hover:bg-[#6c63ff]/5 cursor-grab transition-all group">
                    <span class="text-lg flex-shrink-0">{{ $icon }}</span>
                    <div><div class="text-xs font-semibold text-gray-300 group-hover:text-white">{{ $label }}</div><div class="text-xs text-gray-600">{{ $desc }}</div></div>
                    <span class="ml-auto text-gray-700 group-hover:text-[#6c63ff] text-sm">＋</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- AI GENERATE --}}
        <div class="p-3 border-t border-white/5">
            <div class="bg-gradient-to-r from-[#6c63ff]/10 to-purple-500/5 border border-[#6c63ff]/20 rounded-xl p-3">
                <div class="text-xs font-semibold text-[#6c63ff] mb-2">🤖 AI Generate</div>
                <input x-model="aiPrompt" type="text" placeholder="Describe a section..."
                    class="w-full bg-[#0a0a0f] border border-white/10 rounded-lg px-2.5 py-2 text-xs text-gray-300 placeholder-gray-600 outline-none focus:border-[#6c63ff]/40 mb-2"/>
                <button @click="aiGenerate()" :disabled="!aiPrompt || aiGenerating"
                    class="w-full py-2 rounded-lg bg-[#6c63ff] text-white text-xs font-semibold hover:opacity-90 disabled:opacity-40 transition-all flex items-center justify-center gap-1">
                    <span x-show="aiGenerating" class="animate-spin">⟳</span>
                    <span x-text="aiGenerating ? 'Generating...' : '✨ Generate'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- CENTER: CANVAS --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Toolbar --}}
        <div class="flex items-center gap-2 px-4 py-2.5 bg-[#111118] border-b border-white/5 flex-shrink-0">
            <div class="flex bg-[#1a1a24] border border-white/8 rounded-lg p-0.5 gap-0.5">
                <button @click="viewport='desktop'" :class="viewport==='desktop' ? 'bg-[#6c63ff] text-white':'text-gray-500'" class="px-3 py-1.5 rounded-md text-xs transition-all">🖥 Desktop</button>
                <button @click="viewport='tablet'"  :class="viewport==='tablet'  ? 'bg-[#6c63ff] text-white':'text-gray-500'" class="px-3 py-1.5 rounded-md text-xs transition-all">📱 Tablet</button>
                <button @click="viewport='mobile'"  :class="viewport==='mobile'  ? 'bg-[#6c63ff] text-white':'text-gray-500'" class="px-3 py-1.5 rounded-md text-xs transition-all">📲 Mobile</button>
            </div>
            <div class="flex items-center gap-1 ml-2">
                <button @click="undo()" class="p-2 text-gray-500 hover:text-gray-300 transition-colors text-sm" title="Undo">↩</button>
                <button @click="redo()" class="p-2 text-gray-500 hover:text-gray-300 transition-colors text-sm" title="Redo">↪</button>
            </div>
            <div class="ml-auto flex gap-2">
                <button @click="previewMode = !previewMode"
                    :class="previewMode ? 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400' : 'border-white/10 text-gray-400'"
                    class="px-3 py-1.5 rounded-lg border text-xs transition-all">
                    <span x-text="previewMode ? '✏️ Edit' : '👁 Preview'"></span>
                </button>
                <button @click="exportCode()" class="px-3 py-1.5 rounded-lg border border-white/10 text-xs text-gray-400 hover:border-white/20 transition-all">
                    &lt;/&gt; Export Code
                </button>
                <button class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-xs font-semibold hover:opacity-90 transition-all">
                    💾 Save Page
                </button>
            </div>
        </div>

        {{-- Canvas --}}
        <div class="flex-1 overflow-auto bg-[#0d0d14] p-8">
            <div class="mx-auto bg-white rounded-xl overflow-hidden shadow-2xl transition-all duration-300"
                :style="viewport === 'desktop' ? 'max-width:100%' : (viewport === 'tablet' ? 'max-width:768px' : 'max-width:375px')">

                {{-- Drop zone when empty --}}
                <div x-show="blocks.length === 0"
                    @dragover.prevent @drop="dropBlock($event)"
                    class="min-h-[400px] flex flex-col items-center justify-center border-4 border-dashed border-gray-200 m-6 rounded-2xl">
                    <div class="text-5xl mb-4">🧩</div>
                    <h3 class="font-bold text-gray-800 text-xl mb-2">Drop blocks here</h3>
                    <p class="text-gray-400 text-sm">Drag blocks from the left panel, or click to add</p>
                    <button @click="addBlock('hero')" class="mt-4 px-6 py-2.5 rounded-xl bg-[#6c63ff] text-white text-sm font-semibold hover:opacity-90 transition-all">
                        ＋ Add Hero Block
                    </button>
                </div>

                {{-- Rendered blocks --}}
                <template x-for="(block, i) in blocks" :key="block.id">
                    <div class="relative group"
                        @click="!previewMode && selectBlock(i)"
                        :class="selectedBlock === i && !previewMode ? 'ring-2 ring-[#6c63ff] ring-offset-0' : ''">

                        {{-- Block controls --}}
                        <div x-show="!previewMode" class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                            <button @click.stop="moveBlock(i, -1)" class="w-6 h-6 bg-[#6c63ff] text-white rounded text-xs hover:bg-[#5a52e0] transition-colors">↑</button>
                            <button @click.stop="moveBlock(i, 1)"  class="w-6 h-6 bg-[#6c63ff] text-white rounded text-xs hover:bg-[#5a52e0] transition-colors">↓</button>
                            <button @click.stop="duplicateBlock(i)" class="w-6 h-6 bg-emerald-500 text-white rounded text-xs hover:bg-emerald-600 transition-colors">⧉</button>
                            <button @click.stop="removeBlock(i)"   class="w-6 h-6 bg-red-500 text-white rounded text-xs hover:bg-red-600 transition-colors">✕</button>
                        </div>

                        {{-- Block label --}}
                        <div x-show="!previewMode" class="absolute top-2 left-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-xs bg-[#6c63ff] text-white px-2 py-0.5 rounded-full font-mono" x-text="block.type"></span>
                        </div>

                        {{-- Block HTML output --}}
                        <div x-html="renderBlock(block)"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- RIGHT: PROPERTIES PANEL --}}
    <div class="w-64 bg-[#111118] border-l border-white/5 overflow-y-auto flex-shrink-0" x-show="selectedBlock !== null && !previewMode">
        <div class="px-4 py-3 border-b border-white/5 flex items-center justify-between">
            <h2 class="font-semibold text-white text-sm">⚙️ Properties</h2>
            <button @click="selectedBlock = null" class="text-gray-600 hover:text-gray-400 text-lg">✕</button>
        </div>

        <div class="p-4" x-show="selectedBlock !== null && blocks[selectedBlock]">
            <div class="text-xs text-gray-500 mb-4 font-mono uppercase tracking-wider" x-text="blocks[selectedBlock]?.type"></div>

            {{-- Common properties --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wider">Heading Text</label>
                    <input type="text" :value="blocks[selectedBlock]?.props?.heading"
                        @input="updateProp('heading', $event.target.value)"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-200 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wider">Subheading</label>
                    <textarea rows="2" :value="blocks[selectedBlock]?.props?.subheading"
                        @input="updateProp('subheading', $event.target.value)"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-200 outline-none focus:border-[#6c63ff]/50 transition-colors resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wider">Button Text</label>
                    <input type="text" :value="blocks[selectedBlock]?.props?.btn_text"
                        @input="updateProp('btn_text', $event.target.value)"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-200 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wider">Background</label>
                    <div class="flex gap-2">
                        <input type="color" :value="blocks[selectedBlock]?.props?.bg_color || '#111827'"
                            @input="updateProp('bg_color', $event.target.value)"
                            class="w-10 h-9 rounded-lg border border-white/10 cursor-pointer bg-transparent"/>
                        <input type="text" :value="blocks[selectedBlock]?.props?.bg_color || '#111827'"
                            @input="updateProp('bg_color', $event.target.value)"
                            class="flex-1 bg-[#1a1a24] border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-200 outline-none font-mono"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wider">Text Color</label>
                    <div class="flex gap-2">
                        <input type="color" :value="blocks[selectedBlock]?.props?.text_color || '#ffffff'"
                            @input="updateProp('text_color', $event.target.value)"
                            class="w-10 h-9 rounded-lg border border-white/10 cursor-pointer bg-transparent"/>
                        <input type="text" :value="blocks[selectedBlock]?.props?.text_color || '#ffffff'"
                            @input="updateProp('text_color', $event.target.value)"
                            class="flex-1 bg-[#1a1a24] border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-200 outline-none font-mono"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wider">Padding</label>
                    <select @change="updateProp('padding', $event.target.value)"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-200 outline-none cursor-pointer">
                        <option value="py-8">Small (py-8)</option>
                        <option value="py-16" selected>Medium (py-16)</option>
                        <option value="py-24">Large (py-24)</option>
                        <option value="py-32">XL (py-32)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function pageBuilder() {
    return {
        paletteTab: 'layout',
        viewport: 'desktop',
        previewMode: false,
        blocks: [],
        selectedBlock: null,
        draggingType: null,
        aiPrompt: '',
        aiGenerating: false,
        history: [],
        future: [],

        addBlock(type) {
            this.saveHistory();
            this.blocks.push({
                id: Date.now(),
                type,
                props: this.defaultProps(type),
            });
            this.selectedBlock = this.blocks.length - 1;
        },

        dragBlock(type) { this.draggingType = type; },
        dropBlock(e) {
            e.preventDefault();
            if (this.draggingType) { this.addBlock(this.draggingType); this.draggingType = null; }
        },

        selectBlock(i) { this.selectedBlock = i; },
        removeBlock(i) { this.saveHistory(); this.blocks.splice(i, 1); this.selectedBlock = null; },
        duplicateBlock(i) { this.saveHistory(); this.blocks.splice(i+1, 0, {...this.blocks[i], id: Date.now()}); },

        moveBlock(i, dir) {
            const j = i + dir;
            if (j < 0 || j >= this.blocks.length) return;
            this.saveHistory();
            [this.blocks[i], this.blocks[j]] = [this.blocks[j], this.blocks[i]];
            this.selectedBlock = j;
        },

        updateProp(key, value) {
            if (this.selectedBlock === null) return;
            this.blocks[this.selectedBlock].props[key] = value;
        },

        saveHistory() {
            this.history.push(JSON.stringify(this.blocks));
            this.future = [];
        },
        undo() { if(this.history.length) { this.future.push(JSON.stringify(this.blocks)); this.blocks = JSON.parse(this.history.pop()); } },
        redo() { if(this.future.length)  { this.history.push(JSON.stringify(this.blocks)); this.blocks = JSON.parse(this.future.pop()); } },

        async aiGenerate() {
            this.aiGenerating = true;
            await new Promise(r => setTimeout(r, 1500));
            this.addBlock('hero');
            this.aiGenerating = false;
            this.aiPrompt = '';
            if(window.showToast) showToast('✅ Block generated!');
        },

        exportCode() {
            const code = this.blocks.map(b => this.renderBlock(b)).join('\n\n');
            navigator.clipboard.writeText(code);
            if(window.showToast) showToast('Code copied to clipboard!');
        },

        defaultProps(type) {
            const defaults = {
                hero:         { heading: 'Your Headline Here', subheading: 'A compelling description that converts visitors.', btn_text: 'Get Started', bg_color: '#111827', text_color: '#ffffff', padding: 'py-24' },
                features:     { heading: 'Why Choose Us', subheading: 'Three reasons your customers will love you.', bg_color: '#ffffff', text_color: '#111827', padding: 'py-16' },
                pricing:      { heading: 'Simple Pricing', subheading: 'No hidden fees. Cancel anytime.', bg_color: '#f9fafb', text_color: '#111827', padding: 'py-16' },
                testimonials: { heading: 'What Our Customers Say', bg_color: '#ffffff', text_color: '#111827', padding: 'py-16' },
                cta:          { heading: 'Ready to Get Started?', btn_text: 'Start Free Trial', bg_color: '#6c63ff', text_color: '#ffffff', padding: 'py-16' },
                heading:      { heading: 'Section Title', bg_color: '#ffffff', text_color: '#111827', padding: 'py-4' },
                paragraph:    { heading: 'Your text goes here. Click to edit this paragraph.', bg_color: '#ffffff', text_color: '#374151', padding: 'py-4' },
            };
            return defaults[type] || { heading: type, bg_color: '#ffffff', text_color: '#111827', padding: 'py-8' };
        },

        renderBlock(block) {
            const p = block.props;
            const style = `background:${p.bg_color||'#fff'};color:${p.text_color||'#111'}`;

            const templates = {
                hero: `<div class="${p.padding||'py-24'} px-8 text-center" style="${style}">
                    <h1 style="font-size:3rem;font-weight:900;margin-bottom:1rem;line-height:1.1">${p.heading}</h1>
                    <p style="font-size:1.125rem;opacity:0.8;max-width:36rem;margin:0 auto 2rem">${p.subheading}</p>
                    <a href="#" style="display:inline-block;background:#6c63ff;color:#fff;padding:1rem 2.5rem;border-radius:0.75rem;font-weight:700;text-decoration:none">${p.btn_text||'Get Started'}</a>
                </div>`,
                features: `<div class="${p.padding||'py-16'} px-8" style="${style}">
                    <h2 style="font-size:2rem;font-weight:800;text-align:center;margin-bottom:0.75rem">${p.heading}</h2>
                    <p style="text-align:center;opacity:0.7;margin-bottom:3rem">${p.subheading}</p>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;max-width:64rem;margin:0 auto">
                        ${['⚡ Fast','🔒 Secure','🤖 AI-Powered'].map(f=>`<div style="border:1px solid #e5e7eb;border-radius:1rem;padding:1.5rem;text-align:center"><div style="font-size:2rem;margin-bottom:0.75rem">${f.split(' ')[0]}</div><h3 style="font-weight:700;margin-bottom:0.5rem">${f.split(' ').slice(1).join(' ')}</h3><p style="opacity:0.7;font-size:0.875rem">Built-in feature description goes here.</p></div>`).join('')}
                    </div>
                </div>`,
                cta: `<div class="${p.padding||'py-16'} px-8 text-center" style="${style}">
                    <h2 style="font-size:2.5rem;font-weight:900;margin-bottom:1rem">${p.heading}</h2>
                    <a href="#" style="display:inline-block;background:#fff;color:#6c63ff;padding:1rem 2.5rem;border-radius:0.75rem;font-weight:700;text-decoration:none;margin-top:0.5rem">${p.btn_text||'Get Started'}</a>
                </div>`,
                heading: `<div class="${p.padding||'py-4'} px-8" style="${style}"><h2 style="font-size:2rem;font-weight:800">${p.heading}</h2></div>`,
                paragraph: `<div class="${p.padding||'py-4'} px-8" style="${style}"><p style="line-height:1.75;max-width:48rem">${p.heading}</p></div>`,
                divider: `<div style="padding:1rem 2rem"><hr style="border:none;border-top:1px solid #e5e7eb"/></div>`,
                pricing: `<div class="${p.padding||'py-16'} px-8" style="${style}">
                    <h2 style="font-size:2rem;font-weight:800;text-align:center;margin-bottom:0.75rem">${p.heading}</h2>
                    <p style="text-align:center;opacity:0.7;margin-bottom:3rem">${p.subheading}</p>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;max-width:56rem;margin:0 auto">
                        ${[['Starter','$9','Basic features'],['Pro','$29','Everything in Starter'],['Enterprise','$99','Full access']].map(([plan,price,desc],i)=>`<div style="border:${i===1?'2px solid #6c63ff':'1px solid #e5e7eb'};border-radius:1rem;padding:2rem;text-align:center;${i===1?'background:#f5f3ff':''}"><h3 style="font-weight:700;margin-bottom:0.5rem">${plan}</h3><div style="font-size:2.5rem;font-weight:900;color:#6c63ff;margin:1rem 0">${price}<span style="font-size:1rem;color:#6b7280">/mo</span></div><p style="opacity:0.7;font-size:0.875rem;margin-bottom:1.5rem">${desc}</p><a href="#" style="display:block;background:${i===1?'#6c63ff':'#f3f4f6'};color:${i===1?'#fff':'#374151'};padding:0.75rem;border-radius:0.5rem;text-decoration:none;font-weight:600">Choose ${plan}</a></div>`).join('')}
                    </div>
                </div>`,
            };

            return templates[block.type] || `<div class="${p.padding||'py-8'} px-8" style="${style}"><p style="opacity:0.5;text-align:center">[${block.type} block]</p></div>`;
        }
    }
}
</script>
@endpush
