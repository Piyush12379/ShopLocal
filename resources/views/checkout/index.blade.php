@extends('layouts.app')
@section('title', 'Checkout — ShopLocal')

@section('content')
<div class="checkout-layout">

    {{-- LEFT: Checkout form --}}
    <div>
        <div class="checkout-title">Checkout</div>
        <div class="checkout-sub">Fill in your details to place the order</div>

        <form method="POST" action="{{ route('orders.place') }}" id="checkoutForm">
            @csrf

            {{-- Validation errors --}}
            @if($errors->any())
            <div class="alert alert-error" style="margin:0 0 20px">
                <ul style="margin:0;padding-left:16px">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Section 1: Delivery details --}}
            <div class="form-section">
                <div class="form-section-title">
                    <span class="form-step-num">1</span>
                    Delivery details
                </div>

                <div class="form-grid" style="margin-bottom:14px">
                    <div class="form-group">
                        <label>Full name *</label>
                        <input type="text"
                               name="full_name"
                               value="{{ old('full_name', auth()->user()->name) }}"
                               required
                               placeholder="Your full name"/>
                    </div>
                    <div class="form-group">
                        <label>Phone number *</label>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone') }}"
                               required
                               placeholder="10-digit mobile number"/>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:14px">
                    <label>Full address *</label>
                    <input type="text"
                           name="address"
                           value="{{ old('address') }}"
                           required
                           placeholder="House no, Street, Area"/>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text"
                               name="city"
                               value="{{ old('city') }}"
                               required
                               placeholder="City"/>
                    </div>
                    <div class="form-group">
                        <label>State *</label>
                        <input type="text"
                               name="state"
                               value="{{ old('state') }}"
                               required
                               placeholder="State"/>
                    </div>
                </div>

                <div class="form-group" style="margin-top:14px">
                    <label>PIN code *</label>
                    <input type="text"
                           name="pincode"
                           value="{{ old('pincode') }}"
                           required
                           placeholder="6-digit PIN code"
                           style="max-width:200px"/>
                </div>
            </div>

            {{-- Section 2: Payment method --}}
            <div class="form-section">
                <div class="form-section-title">
                    <span class="form-step-num">2</span>
                    Payment method
                </div>

                <div class="payment-methods" id="paymentMethods">
                    <div class="pay-method selected"
                         data-value="cod"
                         onclick="selectPayment(this)">
                        <div class="pm-icon">💵</div>
                        Cash on delivery
                    </div>
                    <div class="pay-method"
                         data-value="upi"
                         onclick="selectPayment(this)">
                        <div class="pm-icon">📱</div>
                        UPI / GPay
                    </div>
                    <div class="pay-method"
                         data-value="card"
                         onclick="selectPayment(this)">
                        <div class="pm-icon">💳</div>
                        Credit / Debit card
                    </div>
                </div>

                {{-- Hidden input that holds the selected value --}}
                <input type="hidden"
                       name="payment_method"
                       id="paymentInput"
                       value="{{ old('payment_method', 'cod') }}"/>

                <div style="font-size:12px;color:var(--muted);margin-top:8px">
                    🔒 All payments are 100% secure and encrypted
                </div>
            </div>

            {{-- Place order button --}}
            <button type="submit"
                    class="btn btn-primary"
                    style="width:100%;justify-content:center;padding:16px;font-size:16px"
                    id="placeOrderBtn">
                Place order — ₹{{ number_format($grand) }}
            </button>

            <a href="{{ route('cart.index') }}"
               class="btn btn-outline btn-sm"
               style="width:100%;justify-content:center;margin-top:10px">
                ← Back to cart
            </a>

        </form>
    </div>

    {{-- RIGHT: Order summary --}}
    <div class="order-summary">
        <div class="os-title">Order summary</div>

        @foreach($cart as $id => $item)
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
            <div style="width:36px;height:36px;background:var(--cream);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
                {{ $item['emoji'] }}
            </div>
            <div style="flex:1;font-size:13px">
                <div style="font-weight:500">{{ Str::limit($item['name'], 24) }}</div>
                <div style="color:var(--muted);font-size:11px">Qty: {{ $item['quantity'] }}</div>
            </div>
            <div style="font-size:13px;font-weight:500">
                ₹{{ number_format($item['price'] * $item['quantity']) }}
            </div>
        </div>
        @endforeach

        <div style="border-top:1px solid var(--border);margin:14px 0"></div>

        <div class="os-row">
            <span>Subtotal</span>
            <span>₹{{ number_format($total) }}</span>
        </div>
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
            <span>₹{{ number_format($grand) }}</span>
        </div>

        <div style="margin-top:16px;padding:12px;background:var(--cream);border-radius:var(--r);font-size:12px;color:var(--muted);line-height:1.6">
            By placing this order you agree to our
            <span style="color:var(--warm)">Terms of Service</span>
            and
            <span style="color:var(--warm)">Privacy Policy</span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Payment method selection
function selectPayment(el) {
    // Remove selected from all
    document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('selected'));
    // Add to clicked
    el.classList.add('selected');
    // Update hidden input
    document.getElementById('paymentInput').value = el.getAttribute('data-value');
}

// Set initial selected state on page load (handles old() after validation error)
document.addEventListener('DOMContentLoaded', function () {
    const current = document.getElementById('paymentInput').value;
    document.querySelectorAll('.pay-method').forEach(m => {
        if (m.getAttribute('data-value') === current) {
            m.classList.add('selected');
        } else {
            m.classList.remove('selected');
        }
    });
});

// Prevent double submit
document.getElementById('checkoutForm').addEventListener('submit', function () {
    const btn = document.getElementById('placeOrderBtn');
    btn.disabled     = true;
    btn.textContent  = 'Placing order...';
});
</script>
@endpush