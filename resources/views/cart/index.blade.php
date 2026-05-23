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
        
        <div class="os-row">
            <span>Subtotal</span>
            <span>₹{{ number_format($total) }}</span>
        </div>

        {{-- Coupon discount --}}
        @if($coupon && $discount > 0)
        <div class="os-row" style="color:#1D9E75">
            <span>
                Coupon ({{ $coupon['code'] }})
                <button onclick="removeCoupon()"
                        style="background:none;border:none;color:#C85A3A;cursor:pointer;
                               font-size:11px;margin-left:4px;padding:0">✕ Remove</button>
            </span>
            <span>−₹{{ number_format($discount) }}</span>
        </div>
        @endif

        <div class="os-row">
            <span>Delivery</span>
            @if($delivery == 0)
                <span style="color:#1D9E75;font-weight:500">Free</span>
            @else
                <span>₹{{ $delivery }}</span>
            @endif
        </div>

        <div class="os-row total">
            <span>Total</span>
            <span id="grandTotal">₹{{ number_format($grand) }}</span>
        </div>

        {{-- RESTORED: Free delivery upsell message --}}
        @if(($total - $discount) < 500)
        <div style="font-size:12px;color:var(--muted);text-align:center;margin:8px 0 14px">
            Add ₹{{ number_format(500 - ($total - $discount)) }} more for free delivery
        </div>
        @endif

        {{-- Coupon input --}}
        @if(!$coupon)
        <div style="margin:14px 0">
            <div style="font-size:12px;font-weight:500;margin-bottom:6px;color:var(--muted)">
                Have a coupon?
            </div>
            <div style="display:flex;gap:6px">
                <input type="text"
                       id="couponCode"
                       placeholder="Enter coupon code"
                       style="flex:1;padding:8px 12px;border:1px solid var(--border);
                              border-radius:var(--r);font-size:13px;text-transform:uppercase;
                              font-family:var(--ff-body)"/>
                <button onclick="applyCoupon()"
                        class="btn btn-warm btn-sm">Apply</button>
            </div>
            <div id="couponMsg" style="font-size:12px;margin-top:6px;display:none"></div>
        </div>
        @else
        <div style="background:#E1F5EE;border:1px solid #9FE1CB;border-radius:var(--r);
                    padding:8px 12px;font-size:12px;color:#085041;margin:14px 0;
                    display:flex;justify-content:space-between;align-items:center">
            <span>🎉 Coupon <strong>{{ $coupon['code'] }}</strong> applied</span>
            <span>−₹{{ number_format($discount) }}</span>
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

        <div style="display:flex;justify-content:center;gap:16px;
                    margin-top:16px;font-size:11px;color:var(--muted)">
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
     data-coupon-apply-url="{{ route('coupon.apply') }}"
     data-coupon-remove-url="{{ route('coupon.remove') }}"
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

// ── Apply coupon ──────────────────────────────────────────
function applyCoupon() {
    const code  = document.getElementById('couponCode').value.trim();
    const msg   = document.getElementById('couponMsg');
    const routes = document.getElementById('cartRoutes').dataset;

    if (!code) {
        msg.style.display = 'block';
        msg.style.color   = '#C85A3A';
        msg.textContent   = 'Please enter a coupon code.';
        return;
    }

    fetch(routes.couponApplyUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
        body: JSON.stringify({ code: code }),
    })
    .then(r => r.json())
    .then(data => {
        msg.style.display = 'block';
        if (data.success) {
            msg.style.color = '#1D9E75';
            msg.textContent = data.message;
            // Reload to show updated totals
            setTimeout(() => location.reload(), 800);
        } else {
            msg.style.color = '#C85A3A';
            msg.textContent = data.message;
        }
    });
}

// ── Remove coupon ─────────────────────────────────────────
function removeCoupon() {
    const routes = document.getElementById('cartRoutes').dataset;

    fetch(routes.couponRemoveUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept':       'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
    });
}

// Allow pressing Enter in coupon input
document.getElementById('couponCode')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') { 
        e.preventDefault(); 
        applyCoupon(); 
    }
});
</script>
@endpush