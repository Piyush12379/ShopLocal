@extends('layouts.app')
@section('title', 'ShopLocal — Handcrafted Goods')

@section('content')

{{-- ═══════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════ --}}
<section class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <div class="hero-eyebrow">
                <span class="hero-dot"></span>
                100+ local artisans
            </div>
            <h1 class="hero-title">
                Discover<br>
                <em>handcrafted</em><br>
                treasures
            </h1>
            <p class="hero-subtitle">
                Support small businesses and find unique handmade
                products from artisans across India.
            </p>
            <div class="hero-actions">
                <a href="#products" class="btn btn-primary">Shop now</a>
                <a href="#categories" class="btn btn-outline">Browse categories</a>
            </div>
        </div>

        {{-- Featured product card in hero --}}
        @if($featuredProduct)
        <div class="hero-visual">
            <div class="hero-product-card">
                <div class="hero-product-img">{{ $featuredProduct->emoji }}</div>
                <div class="hero-product-tag">{{ $featuredProduct->category->name }}</div>
                <div class="hero-product-name">{{ $featuredProduct->name }}</div>
                <div class="hero-product-price">₹{{ number_format($featuredProduct->price) }}</div>
            </div>
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════
     TRUST BAR
═══════════════════════════════════════ --}}
<div class="trust-bar">
    <div class="trust-item"><span>🚚</span><div><strong>Free delivery</strong><small>Orders over ₹500</small></div></div>
    <div class="trust-item"><span>🔒</span><div><strong>Secure payment</strong><small>100% protected</small></div></div>
    <div class="trust-item"><span>↩️</span><div><strong>Easy returns</strong><small>7-day policy</small></div></div>
    <div class="trust-item"><span>🤝</span><div><strong>Support artisans</strong><small>Direct from makers</small></div></div>
</div>

{{-- ═══════════════════════════════════════
     CATEGORY FILTER
═══════════════════════════════════════ --}}
<section class="section" id="categories">
    <div class="section-header">
        <div class="section-label">Browse by category</div>
        <h2 class="section-title">Shop by category</h2>
        <p class="section-sub">Find exactly what you're looking for</p>
    </div>

    <div class="categories-grid">
        {{-- "All" button --}}
        <a href="{{ route('home') }}"
           class="category-card {{ !request('category') ? 'category-active' : '' }}">
            <div class="category-emoji">🛍️</div>
            <div class="category-name">All</div>
            <div class="category-count">{{ $products->count() }} items</div>
        </a>

        {{-- Dynamic categories from DB --}}
        @foreach($categories as $category)
            @if($category->products_count > 0)
            <a href="{{ route('home', ['category' => $category->slug]) }}"
               class="category-card {{ request('category') == $category->slug ? 'category-active' : '' }}">
                <div class="category-emoji">{{ $category->emoji }}</div>
                <div class="category-name">{{ $category->name }}</div>
                <div class="category-count">{{ $category->products_count }} items</div>
            </a>
            @endif
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════
     PRODUCTS GRID + NEW FILTER BAR
═══════════════════════════════════════ --}}
<section class="section" id="products">
    <div class="section-header">
        <div class="section-label">
            {{ request('category') ? $categories->where('slug', request('category'))->first()?->name : 'All products' }}
        </div>
        <h2 class="section-title">
            @if(request('search'))
                Results for "{{ request('search') }}"
            @else
                Featured products
            @endif
        </h2>
        <p class="section-sub">{{ $products->count() }} products found</p>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('home') }}#products" id="filterForm">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        
        <div class="filter-bar">
            {{-- Search --}}
            <div class="filter-group" style="flex:2;min-width:180px">
                <label class="filter-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="search-input" style="width:100%"/>
            </div>
            
            {{-- Min price --}}
            <div class="filter-group">
                <label class="filter-label">Min price (₹)</label>
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ (int)($priceStats->min_price ?? 0) }}" min="0" class="search-input" style="width:110px"/>
            </div>
            
            {{-- Max price --}}
            <div class="filter-group">
                <label class="filter-label">Max price (₹)</label>
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ (int)($priceStats->max_price ?? 9999) }}" min="0" class="search-input" style="width:110px"/>
            </div>
            
            {{-- Sort --}}
            <div class="filter-group">
                <label class="filter-label">Sort by</label>
                <select name="sort" class="search-input" style="width:150px"
                 onchange="submitFilter()">
                    <option value="newest" {{ request('sort','newest') == 'newest' ? 'selected' : '' }}>Newest first</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A → Z</option>
                </select>
            </div>
            
            {{-- In stock only --}}
            <div class="filter-group" style="justify-content:flex-end;align-self:flex-end;padding-bottom:2px">
                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;white-space:nowrap">
                  <input type="checkbox"
                     name="in_stock"
                     value="1"
                     {{ request('in_stock') ? 'checked' : '' }}
                      onchange="submitFilter()"
                       style="width:15px;height:15px"/>
                    In stock only
                </label>
            </div>
            
            {{-- Apply + Clear --}}
            <div class="filter-group" style="align-self:flex-end;display:flex;gap:8px">
                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                @if(request()->hasAny(['search','min_price','max_price','sort','in_stock']))
                    <a href="{{ route('home', request('category') ? ['category'=>request('category')] : []) }}" class="btn btn-outline btn-sm">Clear</a>
                @endif
            </div>
        </div>

        {{-- Active filter pills --}}
        @if(request()->hasAny(['search','min_price','max_price','in_stock']))
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px">
            @if(request('search')) <span class="filter-pill">Search: "{{ request('search') }}"</span> @endif
            @if(request('min_price') || request('max_price'))
            <span class="filter-pill">Price: ₹{{ request('min_price',0) }} – ₹{{ request('max_price','any') }}</span>
            @endif
            @if(request('in_stock')) <span class="filter-pill">In stock only</span> @endif
        </div>
        @endif
    </form>

    {{-- Products --}}
    @if($products->isEmpty())
        <div class="empty-state">
            <div style="font-size:48px; margin-bottom:16px;">🔍</div>
            <h3>No products found</h3>
            <p>Try a different category or search term</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:16px;">View all products</a>
        </div>
    @else
        <div class="products-grid">
            @foreach($products as $product)
            <div class="product-card">
                <div class="product-img">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"/>
                    @else
                        <span class="product-emoji">{{ $product->emoji }}</span>
                    @endif

                    @if($product->isOnSale())
                        <span class="badge badge-sale">{{ $product->discountPercent() }}% off</span>
                    @elseif($product->created_at->diffInDays() < 7)
                        <span class="badge badge-new">New</span>
                    @elseif($product->stock < 5 && $product->stock > 0)
                        <span class="badge badge-hot">Low stock</span>
                    @endif

                    @if(!$product->inStock())
                        <div class="out-of-stock-overlay">Out of stock</div>
                    @endif
                </div>

                <div class="product-body">
                    <div class="product-category">{{ $product->category->name }}</div>
                    <h3 class="product-name">
                        <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                    </h3>
                    <p class="product-desc">{{ Str::limit($product->description, 60) }}</p>

                    <div class="product-footer">
                        <div class="product-price">
                            @if($product->isOnSale())
                                <span class="price-old">₹{{ number_format($product->old_price) }}</span>
                            @endif
                            <span class="price-current">₹{{ number_format($product->price) }}</span>
                        </div>

                        <div style="display:flex;align-items:center;gap:8px">
                            @auth
                                @if(auth()->user()->isCustomer())
                                <button class="wishlist-btn" data-id="{{ $product->id }}" data-wishlisted="{{ in_array($product->id, $wishlistIds) ? 'true' : 'false' }}" onclick="toggleWishlist(this)" style="width:32px;height:32px;border-radius:50%;background:{{ in_array($product->id, $wishlistIds) ? '#FAECE7' : 'var(--cream)' }};color:{{ in_array($product->id, $wishlistIds) ? '#C85A3A' : 'var(--muted)' }};border:1px solid {{ in_array($product->id, $wishlistIds) ? '#F5C4B3' : 'var(--border)' }};font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;flex-shrink:0">
                                    {{ in_array($product->id, $wishlistIds) ? '♥' : '♡' }}
                                </button>
                                @endif
                            @endauth

                            @auth
                                @if(auth()->user()->isCustomer() && $product->inStock())
                                    <button class="add-to-cart-btn" data-id="{{ $product->id }}" onclick="addToCart(this)" title="Add to cart">+</button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="add-to-cart-btn" title="Login to buy">+</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</section>

{{-- Hidden route URLs for JS --}}
<div id="cartRoutes"
     data-add-url="{{ route('cart.add') }}"
     data-update-url="{{ route('cart.update') }}"
     data-remove-url="{{ route('cart.remove') }}"
     data-wishlist-url="{{ route('wishlist.toggle') }}"
     style="display:none">
</div>

@endsection

@push('scripts')
<script>
// ── Filter submit helper ─────────────────────────────────
function submitFilter() {
    document.getElementById('filterForm').submit();
}

// ── Auto scroll to #products when a filter is active ─────
document.addEventListener('DOMContentLoaded', function () {
    const params    = new URLSearchParams(window.location.search);
    const hasFilter = params.has('search')   ||
                      params.has('min_price') ||
                      params.has('max_price') ||
                      params.has('category')  ||
                      params.has('sort')      ||
                      params.has('in_stock');

    if (hasFilter) {
        const section = document.getElementById('products');
        if (section) {
            setTimeout(() => {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 80);
        }
    }
});

// ── Add to cart via AJAX ─────────────────────────────────
function addToCart(btn) {
    const productId = btn.getAttribute('data-id');
    btn.disabled    = true;
    btn.textContent = '...';

    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
        body: JSON.stringify({ product_id: productId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.textContent      = '✓';
            btn.style.background = '#1D9E75';
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cart_count;
            if (typeof showToast === 'function') showToast(data.message, '🛒');
        } else {
            btn.textContent      = '!';
            btn.style.background = '#C85A3A';
            if (typeof showToast === 'function') showToast(data.message, '⚠️');
        }
        setTimeout(() => {
            btn.textContent      = '+';
            btn.style.background = '';
            btn.disabled         = false;
        }, 1500);
    })
    .catch(err => {
        console.error('Cart error:', err);
        btn.textContent      = '+';
        btn.style.background = '';
        btn.disabled         = false;
    });
}

// ── Wishlist toggle ──────────────────────────────────────
function toggleWishlist(btn) {
    const productId  = btn.getAttribute('data-id');
    const routes     = document.getElementById('cartRoutes').dataset;

    fetch(routes.wishlistUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
        body: JSON.stringify({ product_id: productId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.in_wishlist) {
                btn.textContent       = '♥';
                btn.style.background  = '#FAECE7';
                btn.style.color       = '#C85A3A';
                btn.style.borderColor = '#F5C4B3';
                btn.setAttribute('data-wishlisted', 'true');
            } else {
                btn.textContent       = '♡';
                btn.style.background  = 'var(--cream)';
                btn.style.color       = 'var(--muted)';
                btn.style.borderColor = 'var(--border)';
                btn.setAttribute('data-wishlisted', 'false');
            }
            if (typeof showToast === 'function') {
                showToast(data.message, data.in_wishlist ? '❤️' : '🤍');
            }
        }
    })
    .catch(err => console.error('Wishlist error:', err));
}
</script>
@endpush