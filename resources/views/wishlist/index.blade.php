@extends('layouts.app')
@section('title', 'My Wishlist — ShopLocal')

@section('content')
<div style="max-width:1100px;margin:0 auto;padding:48px 24px">

    <div style="margin-bottom:32px;display:flex;align-items:center;
                justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:var(--ff-head);font-size:40px;font-weight:700;margin-bottom:6px">
                My wishlist ❤️
            </h1>
            <p style="color:var(--muted)">
                {{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }} saved
            </p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline btn-sm">← Continue shopping</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px">{{ session('success') }}</div>
    @endif

    @if($wishlistItems->isEmpty())
        <div class="empty-state">
            <div style="font-size:64px;margin-bottom:16px">🤍</div>
            <h3>Your wishlist is empty</h3>
            <p style="margin-top:8px">Click the ♡ heart button on any product to save it here</p>
            <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:24px">
                Browse products
            </a>
        </div>
    @else
        <div class="products-grid">
            @foreach($wishlistItems as $item)
            @if($item->product)
            <div class="product-card">

                {{-- Product image --}}
                <div class="product-img">
                    @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}"
                             alt="{{ $item->product->name }}"/>
                    @else
                        <span class="product-emoji">{{ $item->product->emoji }}</span>
                    @endif

                    @if($item->product->isOnSale())
                        <span class="badge badge-sale">{{ $item->product->discountPercent() }}% off</span>
                    @endif

                    @if(!$item->product->inStock())
                        <div class="out-of-stock-overlay">Out of stock</div>
                    @endif

                    {{-- Remove from wishlist button --}}
                    <form method="POST"
                          action="{{ route('wishlist.remove') }}"
                          style="position:absolute;top:10px;right:10px">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="product_id" value="{{ $item->product_id }}"/>
                        <button type="submit"
                                style="width:28px;height:28px;border-radius:50%;
                                       background:#FAECE7;color:#C85A3A;
                                       border:none;font-size:14px;cursor:pointer;
                                       display:flex;align-items:center;justify-content:center"
                                title="Remove from wishlist">
                            ♥
                        </button>
                    </form>
                </div>

                {{-- Product info --}}
                <div class="product-body">
                    <div class="product-category">{{ $item->product->category->name }}</div>
                    <h3 class="product-name">
                        <a href="{{ route('products.show', $item->product) }}">
                            {{ $item->product->name }}
                        </a>
                    </h3>
                    <p class="product-desc">
                        {{ Str::limit($item->product->description, 60) }}
                    </p>

                    <div class="product-footer">
                        <div class="product-price">
                            @if($item->product->isOnSale())
                                <span class="price-old">
                                    ₹{{ number_format($item->product->old_price) }}
                                </span>
                            @endif
                            <span class="price-current">
                                ₹{{ number_format($item->product->price) }}
                            </span>
                        </div>

                        @if($item->product->inStock())
                        <button
                            class="add-to-cart-btn"
                            data-id="{{ $item->product->id }}"
                            onclick="addToCart(this)"
                            title="Add to cart">
                            +
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    @endif
</div>

{{-- Hidden routes div --}}
<div id="cartRoutes"
     data-add-url="{{ route('cart.add') }}"
     style="display:none">
</div>

@endsection

@push('scripts')
<script>
function addToCart(btn) {
    const productId = btn.getAttribute('data-id');
    btn.disabled    = true;
    btn.textContent = '...';

    fetch(document.getElementById('cartRoutes').dataset.addUrl, {
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
        }
        setTimeout(() => {
            btn.textContent      = '+';
            btn.style.background = '';
            btn.disabled         = false;
        }, 1500);
    });
}
</script>
@endpush