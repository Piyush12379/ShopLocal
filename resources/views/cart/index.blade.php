@extends('layouts.app')
@section('title', 'Your Cart — ShopLocal')

@section('content')
<div class="cart-layout">

    {{-- ═══════════════════════════════════════
         LEFT: Cart items
    ═══════════════════════════════════════ --}}
    <div>
        <h1 class="cart-title">
            Your cart
            <span style="font-family:var(--ff-body);font-size:18px;font-weight:400;color:var(--muted)">
                ({{ $count }} {{ Str::plural('item', $count) }})
            </span>
        </h1>

        @if(empty($cart))
            {{-- Empty cart state --}}
            <div class="empty-state">
                <div style="font-size:64px;margin-bottom:16px">🛒</div>
                <h3>Your cart is empty</h3>
                <p style="margin-top:8px">Browse our products and add something you love</p>
                <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:24px">
                    Browse products
                </a>
            </div>

        @else
            <div class="cart-items" id="cartItems">
                @foreach($cart as $id => $item)
                <div class="cart-item" id="cart-item-{{ $id }}">

                    {{-- Product image / emoji --}}
                    <div class="ci-img">
                        @if($item['image'])
                            <img src="{{ asset('storage/' . $item['image']) }}"
                                 alt="{{ $item['name'] }}"
                                 style="width:100%;height:100%;object-fit:cover;border-radius:10px"/>
                        @else
                            {{ $item['emoji'] }}
                        @endif
                    </div>

                    {{-- Product info --}}
                    <div class="ci-info">
                        <div class="ci-name">{{ $item['name'] }}</div>
                        <div class="ci-cat">{{ $item['category'] }}</div>

                        <div class="ci-bottom">

                            {{-- ✅ FIX: product id in data attribute, not inside onclick() --}}
                            <button class="qty-btn"
                                    data-id="{{ $id }}"
                                    data-action="minus"
                                    onclick="updateQty(this)">−</button>

                            <span class="qty-val" id="qty-{{ $id }}">
                                {{ $item['quantity'] }}
                            </span>

                            <button class="qty-btn"
                                    data-id="{{ $id }}"
                                    data-action="plus"
                                    data-stock="{{ $item['stock'] }}"
                                    onclick="updateQty(this)"
                                    {{ $item['quantity'] >= $item['stock'] ? 'disabled' : '' }}>+</button>

                            {{-- Item subtotal --}}
                            <span class="ci-price" id="item-total-{{ $id }}">
                                ₹{{ number_format($item['price'] * $item['quantity']) }}
                            </span>

                            {{-- ✅ FIX: id in data attribute --}}
                            <button class="ci-remove"
                                    data-id="{{ $id }}"
                                    onclick="removeItem(this)">
                                Remove
                            </button>

                        </div>

                        <div style="font-size:12px;color:var(--muted);margin-top:4px">
                            ₹{{ number_format($item['price']) }} each
                        </div>
                    </div>

                </div>
                @endforeach
            </div>

            {{-- Clear cart --}}
            <form method="POST" action="{{ route('cart.clear') }}" style="margin-top:16px">
                @csrf
                <button type="submit"
                        class="btn btn-outline btn-sm"
                        onclick="return confirm('Clear entire cart?')">
                    Clear cart
                </button>
            </form>

        @endif
    </div>

    {{-- ═══════════════════════════════════════
         RIGHT: Order summary
    ═══════════════════════════════════════ --}}
    @if(!empty($cart))
    <div class="order-summary">
        <div class="os-title">Order summary</div>

        @foreach($cart as $id => $item)
        <div class="os-row">
            <span>{{ Str::limit($item['name'], 22) }} × {{ $item['quantity'] }}</span>
            <span>₹{{ number_format($item['price'] * $item['quantity']) }}</span>
        </div>
        @endforeach

        <div style="border-top:1px solid var(--border);margin:12px 0"></div>

        {{-- Delivery --}}
        <div class="os-row">
            <span>Delivery</span>
            {{-- ✅ FIX: no Blade inside style — use class instead --}}
            @if($total >= 500)
                <span style="color:#1D9E75;font-weight:500">Free</span>
            @else
                <span>₹50</span>
            @endif
        </div>

        {{-- Total --}}
        <div class="os-row total">
            <span>Total</span>
            <span id="cartGrandTotal">
                ₹{{ number_format($total + ($total >= 500 ? 0 : 50)) }}
            </span>
        </div>

        @if($total < 500)
        <div style="font-size:12px;color:var(--muted);text-align:center;margin:8px 0 14px">
            Add ₹{{ number_format(500 - $total) }} more for free delivery
        </div>
        @endif

        {{-- Checkout button --}}
        <a href="{{ route('checkout.index') }}"
           class="btn btn-primary"
           style="width:100%;justify-content:center;padding:14px;margin-top:8px">
            Proceed to checkout →
        </a>

        <a href="{{ route('home') }}"
           class="btn btn-outline btn-sm"
           style="width:100%;justify-content:center;margin-top:10px">
            ← Continue shopping
        </a>

        {{-- Trust badges --}}
        <div style="display:flex;justify-content:center;gap:16px;margin-top:16px;font-size:11px;color:var(--muted)">
            <span>🔒 Secure</span>
            <span>↩️ 7-day returns</span>
            <span>🚚 Fast delivery</span>
        </div>
    </div>
    @endif

</div>

{{-- Store route URLs as data attributes on body — avoids Blade inside JS strings --}}
<div id="cartRoutes"
     data-add-url="{{ route('cart.add') }}"
     data-update-url="{{ route('cart.update') }}"
     data-remove-url="{{ route('cart.remove') }}"
     style="display:none">
</div>

@endsection

@push('scripts')
<script>
// Read routes from data attributes — no Blade inside JS
const routes = document.getElementById('cartRoutes').dataset;
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;

// ── Update quantity ──────────────────────────────────
function updateQty(btn) {
    const id     = btn.getAttribute('data-id');
    const action = btn.getAttribute('data-action');
    const qtyEl  = document.getElementById('qty-' + id);
    const current = parseInt(qtyEl.textContent.trim());
    const newQty  = action === 'plus' ? current + 1 : current - 1;

    if (newQty < 1) {
        removeItem(btn);
        return;
    }

    fetch(routes.updateUrl, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: id, quantity: newQty }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            qtyEl.textContent = newQty;
            document.getElementById('item-total-' + id).textContent = '₹' + data.item_total;
            const badge = document.getElementById('cartBadge');
            if (badge) badge.textContent = data.cart_count;
            location.reload();
        }
    })
    .catch(err => console.error('Update error:', err));
}

// ── Remove item ───────────────────────────────────────
function removeItem(btn) {
    const id   = btn.getAttribute('data-id');
    const item = document.getElementById('cart-item-' + id);

    fetch(routes.removeUrl, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: id }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Animate item out
            item.style.transition = 'all 0.3s ease';
            item.style.opacity    = '0';
            item.style.transform  = 'translateX(-20px)';
            setTimeout(() => {
                item.remove();
                const badge = document.getElementById('cartBadge');
                if (badge) badge.textContent = data.cart_count;
                location.reload();
            }, 300);
        }
    })
    .catch(err => console.error('Remove error:', err));
}
</script>
@endpush