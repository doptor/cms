@extends('layouts.app')
@section('title', 'Marketplace')

@section('content')
<div class="p-6 max-w-7xl mx-auto" x-data="marketplace()">

    {{-- HERO --}}
    <div class="relative bg-gradient-to-br from-[#111118] to-[#1a1a24] border border-white/5 rounded-2xl p-8 mb-8 overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#6c63ff] rounded-full blur-[100px] opacity-10"></div>
        <div class="absolute bottom-0 left-1/3 w-48 h-48 bg-[#00d4aa] rounded-full blur-[80px] opacity-8"></div>
        <div class="relative">
            <h1 class="font-display font-bold text-3xl text-white mb-2">🛒 RyaanCMS Marketplace</h1>
            <p class="text-gray-400 text-sm mb-6 max-w-xl">Ready-made Laravel apps, templates & plugins. Install with one click into any project. Build once, sell forever.</p>
            <div class="flex gap-3">
                <div class="flex-1 max-w-sm flex items-center gap-2 bg-[#0a0a0f]/60 border border-white/10 rounded-xl px-4 py-2.5">
                    <span class="text-gray-500">🔍</span>
                    <input x-model="search" type="text" placeholder="Search apps, templates, plugins..."
                        class="flex-1 bg-transparent text-sm text-gray-200 placeholder-gray-600 outline-none"/>
                </div>
                <button @click="showSubmitModal = true"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all">
                    📤 Sell Your App
                </button>
            </div>
        </div>
    </div>

    {{-- FEATURED --}}
    @if($featured->isNotEmpty())
    <div class="mb-8">
        <h2 class="font-display font-bold text-white mb-4">⭐ Featured</h2>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($featured as $item)
            <div class="bg-[#111118] border border-[#6c63ff]/25 rounded-2xl overflow-hidden hover:border-[#6c63ff]/50 transition-all group cursor-pointer" @click="openItem({{ $item->toJson() }})">
                <div class="h-36 bg-gradient-to-br from-[#1a1a24] to-[#0a0a0f] flex items-center justify-center text-5xl relative">
                    {{ ['🛍','📰','💼','🎓','🏠','📊','🎨','🏥','🍕','🚗'][($item->id % 10)] }}
                    <div class="absolute top-3 right-3 bg-amber-500 text-black text-xs font-bold px-2 py-0.5 rounded-full">FEATURED</div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-white mb-1">{{ $item->name }}</h3>
                    <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $item->short_description }}</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1 text-xs text-amber-400">
                            ⭐ {{ number_format($item->rating, 1) }}
                            <span class="text-gray-600">({{ $item->rating_count }})</span>
                        </div>
                        <span class="text-sm font-bold {{ $item->isFree() ? 'text-emerald-400' : 'text-white' }}">
                            {{ $item->isFree() ? 'FREE' : '$'.$item->price }}
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- FILTERS --}}
    <div class="flex items-center gap-2 mb-6 flex-wrap">
        @foreach(['All','E-Commerce','Blog','SaaS','Portfolio','Landing Page','Booking','Plugin'] as $cat)
        <button @click="activeCategory = '{{ $cat }}'"
            :class="activeCategory === '{{ $cat }}' ? 'bg-[#6c63ff] text-white border-[#6c63ff]' : 'border-white/10 text-gray-400 hover:border-white/20 hover:text-gray-300'"
            class="px-4 py-1.5 rounded-lg border text-sm transition-all">{{ $cat }}</button>
        @endforeach
        <div class="ml-auto flex items-center gap-2">
            <select x-model="priceFilter" class="bg-[#111118] border border-white/10 rounded-lg text-xs text-gray-400 px-3 py-1.5 outline-none cursor-pointer">
                <option value="all">All Prices</option>
                <option value="free">Free Only</option>
                <option value="paid">Paid Only</option>
            </select>
            <select x-model="sortBy" class="bg-[#111118] border border-white/10 rounded-lg text-xs text-gray-400 px-3 py-1.5 outline-none cursor-pointer">
                <option value="latest">Latest</option>
                <option value="popular">Most Popular</option>
                <option value="rating">Top Rated</option>
                <option value="price_low">Price: Low to High</option>
            </select>
        </div>
    </div>

    {{-- ITEMS GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @foreach($items as $item)
        <div class="bg-[#111118] border border-white/5 rounded-2xl overflow-hidden hover:border-[#6c63ff]/30 hover:-translate-y-0.5 transition-all group cursor-pointer"
            @click="openItem({{ $item->toJson() }})">
            <div class="h-28 bg-gradient-to-br from-[#1a1a24] to-[#0a0a0f] flex items-center justify-center text-4xl">
                {{ ['🛍','📰','💼','🎓','🏠','📊','🎨','🏥','🍕','🚗','🎮','📱','🔧','💊','📦'][($item->id % 15)] }}
            </div>
            <div class="p-3">
                <h3 class="font-semibold text-white text-sm mb-1 truncate">{{ $item->name }}</h3>
                <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $item->short_description }}</p>
                <div class="flex items-center justify-between">
                    <div class="text-xs text-gray-500">⭐ {{ $item->rating }}</div>
                    <span class="text-sm font-bold {{ $item->isFree() ? 'text-emerald-400' : 'text-white' }}">
                        {{ $item->isFree() ? 'FREE' : '$'.$item->price }}
                    </span>
                </div>
                <button class="mt-2 w-full py-1.5 rounded-lg text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity bg-[#6c63ff]/20 text-[#6c63ff] hover:bg-[#6c63ff]/30">
                    {{ $item->isFree() ? 'Install Free' : 'Get for $'.$item->price }}
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- PAGINATION --}}
    <div class="mt-8">{{ $items->links() }}</div>

    {{-- SELL YOUR APP CTA --}}
    <div class="mt-12 bg-gradient-to-br from-[#6c63ff]/10 to-purple-500/5 border border-[#6c63ff]/20 rounded-2xl p-8 text-center">
        <div class="text-4xl mb-4">💰</div>
        <h2 class="font-display font-bold text-2xl text-white mb-2">Sell Your Apps & Templates</h2>
        <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">Build once with RyaanCMS AI, sell forever. Keep 70% of every sale. Join our growing developer community.</p>
        <div class="flex items-center justify-center gap-6 text-sm text-gray-400 mb-6">
            <div>✅ 70% revenue share</div>
            <div>✅ Global marketplace</div>
            <div>✅ Instant payments</div>
        </div>
        <button @click="showSubmitModal = true"
            class="px-8 py-3 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white font-semibold hover:opacity-90 transition-all">
            Start Selling →
        </button>
    </div>
</div>

{{-- ITEM DETAIL MODAL --}}
<div x-show="selectedItem" x-cloak
    class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="selectedItem = null">
    <div class="bg-[#111118] border border-white/10 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden" x-show="selectedItem" @click.stop>
        <div x-show="selectedItem" class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="font-display font-bold text-xl text-white" x-text="selectedItem?.name"></h2>
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                        <span>⭐ <span x-text="selectedItem?.rating"></span></span>
                        <span>📥 <span x-text="selectedItem?.download_count"></span> installs</span>
                        <span x-text="selectedItem?.category"></span>
                    </div>
                </div>
                <button @click="selectedItem = null" class="text-gray-500 hover:text-white transition-colors text-xl">✕</button>
            </div>

            <p class="text-sm text-gray-400 mb-6" x-text="selectedItem?.description"></p>

            <div class="flex items-center justify-between">
                <div class="text-2xl font-bold" :class="selectedItem?.price == 0 ? 'text-emerald-400' : 'text-white'">
                    <span x-text="selectedItem?.price == 0 ? 'FREE' : '$' + selectedItem?.price"></span>
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-2 rounded-xl border border-white/10 text-sm text-gray-400 hover:border-white/20 transition-all">
                        👁 Preview
                    </button>
                    <button @click="installItem()" :disabled="installing"
                        class="px-6 py-2 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 disabled:opacity-50 transition-all flex items-center gap-2">
                        <span x-show="installing" class="animate-spin">⟳</span>
                        <span x-text="installing ? 'Installing...' : (selectedItem?.price == 0 ? '⬇ Install Free' : '💳 Purchase & Install')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SUBMIT MODAL --}}
<div x-show="showSubmitModal" x-cloak
    class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    @click.self="showSubmitModal = false">
    <div class="bg-[#111118] border border-white/10 rounded-2xl w-full max-w-lg p-8 shadow-2xl" @click.stop>
        <h2 class="font-display font-bold text-xl text-white mb-2">📤 Submit to Marketplace</h2>
        <p class="text-gray-500 text-sm mb-6">Share your creation with thousands of developers. Review takes 24-48 hours.</p>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">App Name</label>
                <input x-model="submitForm.name" type="text" placeholder="e.g. ShopKit Pro"
                    class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Short Description</label>
                <input x-model="submitForm.short_description" type="text" placeholder="One-line description"
                    class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Category</label>
                    <select x-model="submitForm.category" class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 outline-none cursor-pointer">
                        <option>ecommerce</option><option>blog</option><option>saas</option>
                        <option>portfolio</option><option>landing</option><option>booking</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Price (USD)</label>
                    <input x-model="submitForm.price" type="number" min="0" placeholder="0 = Free"
                        class="w-full bg-[#1a1a24] border border-white/10 rounded-xl px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 outline-none focus:border-[#6c63ff]/50 transition-colors"/>
                </div>
            </div>
        </div>

        <div class="bg-[#6c63ff]/8 border border-[#6c63ff]/20 rounded-xl p-3 mt-4 text-xs text-gray-400">
            💡 You keep <strong class="text-white">70%</strong> of every sale. Platform fee: 30%.
        </div>

        <div class="flex gap-3 mt-6">
            <button @click="showSubmitModal = false" class="flex-1 py-2.5 rounded-xl border border-white/10 text-sm text-gray-400 hover:border-white/20 transition-all">Cancel</button>
            <button class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-[#6c63ff] to-purple-600 text-white text-sm font-semibold hover:opacity-90 transition-all">Submit for Review</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function marketplace() {
    return {
        search: '', activeCategory: 'All', priceFilter: 'all', sortBy: 'latest',
        selectedItem: null, installing: false, showSubmitModal: false,
        submitForm: { name: '', short_description: '', category: 'ecommerce', price: 0 },

        openItem(item) { this.selectedItem = item; },

        async installItem() {
            if (!this.selectedItem) return;
            this.installing = true;
            // TODO: prompt user to select project
            await new Promise(r => setTimeout(r, 1500));
            this.installing = false;
            this.selectedItem = null;
            showToast(`✅ ${this.selectedItem?.name || 'Item'} installed!`);
        }
    }
}
</script>
@endpush
