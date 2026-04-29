@extends('layouts.app')
@section('title', $product->name . ' — ShopLocal')

@section('content')
<div style="max-width:1100px;margin:48px auto;padding:0 24px">

    {{-- Back link --}}
    <a href="{{ route('home') }}"
       style="font-size:13px;color:var(--muted);text-decoration:none;display:inline-block;margin-bottom:24px">
       ← Back to shop
    </a>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start">

        {{-- LEFT: Product image --}}
        <div style="background:var(--cream);border-radius:20px;height:380px;
                    display:flex;align-items:center;justify-content:center;
                    font-size:100px;position:relative;overflow:hidden">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                     style="width:100%;height:100%;object-fit:cover"/>
            @else
                {{ $product->emoji }}
            @endif

            @if($product->isOnSale())
                <span style="position:absolute;top:16px;left:16px;background:#FAECE7;
                             color:#993C1D;padding:4px 12px;border-radius:6px;
                             font-size:13px;font-weight:700">
                    {{ $product->discountPercent() }}% OFF
                </span>
            @endif
        </div>

        {{-- RIGHT: Product info --}}
        <div>
            <div style="font-size:12px;color:var(--warm);font-weight:600;
                        text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px">
                {{ $product->category->name }}
            </div>

            <h1 style="font-family:var(--ff-head);font-size:40px;font-weight:700;
                       line-height:1.1;margin-bottom:12px">
                {{ $product->name }}
            </h1>

            {{-- Rating --}}
            @if($product->reviews->count() > 0)
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
                @php $avg = round($product->reviews->avg('rating'), 1) @endphp
                <div style="color:#F59E0B;font-size:18px">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= $avg ? '★' : '☆' }}
                    @endfor
                </div>
                <span style="font-size:14px;color:var(--muted)">
                    {{ $avg }} ({{ $product->reviews->count() }} reviews)
                </span>
            </div>
            @endif

            {{-- Price --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
                <span style="font-family:var(--ff-head);font-size:38px;
                             font-weight:700;color:var(--accent)">
                    ₹{{ number_format($product->price) }}
                </span>
                @if($product->isOnSale())
                <span style="font-size:20px;color:var(--muted);text-decoration:line-through">
                    ₹{{ number_format($product->old_price) }}
                </span>
                @endif
            </div>

            <p style="font-size:15px;color:var(--muted);line-height:1.8;margin-bottom:24px">
                {{ $product->description }}
            </p>

            {{-- Stock status --}}
            <div style="margin-bottom:20px;font-size:14px">
                @if($product->inStock())
                    <span style="color:#2E7D32;font-weight:500">
                        ✓ In stock ({{ $product->stock }} available)
                    </span>
                @else
                    <span style="color:var(--accent);font-weight:500">✗ Out of stock</span>
                @endif
            </div>

            {{-- Vendor --}}
            <div style="font-size:13px;color:var(--muted);margin-bottom:24px">
                Sold by <strong>{{ $product->vendor->name }}</strong>
            </div>

            {{-- Add to cart --}}
            @auth
                @if(auth()->user()->isCustomer() && $product->inStock())
                <button class="btn btn-primary"
                        style="width:100%;justify-content:center;padding:16px;font-size:16px"
                        data-id="{{ $product->id }}"
                        onclick="addToCart(this)">
                    🛒 Add to cart
                </button>
                @elseif(!$product->inStock())
                <button class="btn btn-outline"
                        style="width:100%;justify-content:center;padding:16px" disabled>
                    Out of stock
                </button>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="btn btn-primary"
                   style="width:100%;justify-content:center;padding:16px;font-size:16px">
                    Login to buy
                </a>
            @endauth

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success" style="margin:16px 0 0">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" style="margin:16px 0 0">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    {{-- Reviews section --}}
    <div style="margin-top:60px">
        <h2 style="font-family:var(--ff-head);font-size:32px;font-weight:700;margin-bottom:28px">
            Customer reviews
        </h2>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px">

            {{-- Leave a review --}}
            @auth
                @if(auth()->user()->isCustomer())
                <div style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:24px">
                    <h3 style="font-size:18px;font-weight:600;margin-bottom:16px">Write a review</h3>

                    <form method="POST" action="{{ route('reviews.store', $product) }}">
                        @csrf

                        <div style="margin-bottom:14px">
                            <label style="font-size:13px;font-weight:500;display:block;margin-bottom:6px">
                                Your rating *
                            </label>
                            <div style="display:flex;gap:8px" id="starRating">
                                @for($i = 1; $i <= 5; $i++)
                                <span style="font-size:28px;cursor:pointer;color:#DDD8CE"
                                      data-val="{{ $i }}"
                                      onclick="setRating({{ $i }})">★</span>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value=""/>
                        </div>

                        <div class="form-group" style="margin-bottom:16px">
                            <label>Comment (optional)</label>
                            <textarea name="comment" rows="3"
                                      placeholder="Share your experience...">{{ old('comment') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-warm btn-sm">Submit review</button>
                    </form>
                </div>
                @endif
            @endauth

            {{-- Existing reviews --}}
            <div>
                @if($product->reviews->isEmpty())
                    <div style="text-align:center;padding:40px;color:var(--muted)">
                        <div style="font-size:40px;margin-bottom:12px">⭐</div>
                        <p>No reviews yet. Be the first to review!</p>
                    </div>
                @else
                    <div style="display:flex;flex-direction:column;gap:14px">
                        @foreach($product->reviews as $review)
                        <div style="background:#fff;border:1px solid var(--border);
                                    border-radius:12px;padding:16px">
                            <div style="display:flex;justify-content:space-between;
                                        align-items:center;margin-bottom:8px">
                                <strong style="font-size:14px">{{ $review->user->name }}</strong>
                                <span style="font-size:12px;color:var(--muted)">
                                    {{ $review->created_at->format('d M Y') }}
                                </span>
                            </div>
                            <div style="color:#F59E0B;font-size:16px;margin-bottom:6px">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $review->rating ? '★' : '☆' }}
                                @endfor
                            </div>
                            @if($review->comment)
                                <p style="font-size:14px;color:var(--muted);line-height:1.6">
                                    {{ $review->comment }}
                                </p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Related products --}}
    @if($related->isNotEmpty())
    <div style="margin-top:60px">
        <h2 style="font-family:var(--ff-head);font-size:32px;font-weight:700;margin-bottom:24px">
            You may also like
        </h2>
        <div class="products-grid">
            @foreach($related as $rp)
            <div class="product-card">
                <div class="product-img">
                    @if($rp->image)
                        <img src="{{ asset('storage/' . $rp->image) }}" alt="{{ $rp->name }}"/>
                    @else
                        <span class="product-emoji">{{ $rp->emoji }}</span>
                    @endif
                </div>
                <div class="product-body">
                    <div class="product-category">{{ $rp->category->name }}</div>
                    <h3 class="product-name">
                        <a href="{{ route('products.show', $rp) }}">{{ $rp->name }}</a>
                    </h3>
                    <div class="product-footer">
                        <div class="product-price">
                            <span class="price-current">₹{{ number_format($rp->price) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
// Star rating selector
function setRating(val) {
    document.getElementById('ratingInput').value = val;
    const stars = document.querySelectorAll('#starRating span');
    stars.forEach(s => {
        s.style.color = parseInt(s.getAttribute('data-val')) <= val
            ? '#F59E0B' : '#DDD8CE';
    });
}

// Add to cart (same as homepage)
function addToCart(btn) {
    const productId = btn.getAttribute('data-id');
    btn.disabled = true;
    btn.textContent = '...';
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.textContent = '✓ Added to cart';
            btn.style.background = '#1D9E75';
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cart_count;
            if (typeof showToast === 'function') showToast(data.message, '🛒');
        }
        setTimeout(() => {
            btn.textContent = '🛒 Add to cart';
            btn.style.background = '';
            btn.disabled = false;
        }, 2000);
    });
}
</script>
@endpush