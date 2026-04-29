@extends('layouts.app')
@section('title', 'Register — ShopLocal')

@section('content')
<div style="min-height:80vh; display:flex; align-items:center; justify-content:center; padding:40px 20px;">
    <div style="width:100%; max-width:460px;">

        <div style="text-align:center; margin-bottom:32px;">
            <h1 style="font-family:var(--ff-head); font-size:36px; font-weight:700;">Create account</h1>
            <p style="color:var(--muted); margin-top:8px;">Join ShopLocal as a buyer or seller</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:16px;">
                <ul style="margin:0; padding-left:16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="Your full name"
                    style="width:100%; padding:12px 16px; border:1px solid var(--border); border-radius:var(--r); font-family:var(--ff-body); font-size:15px;"/>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Email address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="you@example.com"
                    style="width:100%; padding:12px 16px; border:1px solid var(--border); border-radius:var(--r); font-family:var(--ff-body); font-size:15px;"/>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Password</label>
                <input type="password" name="password" required
                    placeholder="Minimum 8 characters"
                    style="width:100%; padding:12px 16px; border:1px solid var(--border); border-radius:var(--r); font-family:var(--ff-body); font-size:15px;"/>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Confirm password</label>
                <input type="password" name="password_confirmation" required
                    placeholder="Repeat your password"
                    style="width:100%; padding:12px 16px; border:1px solid var(--border); border-radius:var(--r); font-family:var(--ff-body); font-size:15px;"/>
            </div>

            {{-- Role selection --}}
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:10px;">I want to join as</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">

                    {{-- ✅ FIX: No PHP inside style attribute — JS handles border color --}}
                    <label style="border:2px solid var(--border); border-radius:var(--r); padding:16px; cursor:pointer; text-align:center;"
                           id="label-customer"
                           onclick="highlightRole('customer')">
                        <input type="radio" name="role" value="customer"
                               {{ old('role', 'customer') == 'customer' ? 'checked' : '' }}
                               style="display:none"/>
                        <div style="font-size:28px; margin-bottom:6px;">🛍️</div>
                        <div style="font-weight:500; font-size:14px;">Customer</div>
                        <div style="font-size:12px; color:var(--muted); margin-top:4px;">Browse &amp; buy products</div>
                    </label>

                    <label style="border:2px solid var(--border); border-radius:var(--r); padding:16px; cursor:pointer; text-align:center;"
                           id="label-shopkeeper"
                           onclick="highlightRole('shopkeeper')">
                        <input type="radio" name="role" value="shopkeeper"
                               {{ old('role') == 'shopkeeper' ? 'checked' : '' }}
                               style="display:none"/>
                        <div style="font-size:28px; margin-bottom:6px;">🏪</div>
                        <div style="font-weight:500; font-size:14px;">Shopkeeper</div>
                        <div style="font-size:12px; color:var(--muted); margin-top:4px;">Sell your products</div>
                    </label>

                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:14px;">
                Create account
            </button>
        </form>

        <p style="text-align:center; margin-top:20px; font-size:14px; color:var(--muted);">
            Already have an account?
            <a href="{{ route('login') }}" style="color:var(--warm); font-weight:500;">Sign in</a>
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // ✅ FIX: Highlight correct role card on page load (handles old() repopulation after validation error)
    document.addEventListener('DOMContentLoaded', function () {
        const checked = document.querySelector('input[name="role"]:checked');
        if (checked) highlightRole(checked.value);
    });

    // ✅ FIX: highlightRole now takes the selected value as a parameter — no onchange needed
    function highlightRole(selected) {
        document.getElementById('label-customer').style.borderColor
            = selected === 'customer' ? 'var(--warm)' : 'var(--border)';
        document.getElementById('label-shopkeeper').style.borderColor
            = selected === 'shopkeeper' ? 'var(--warm)' : 'var(--border)';

        // Also check the hidden radio button so the form submits correctly
        const radio = document.querySelector('input[value="' + selected + '"]');
        if (radio) radio.checked = true;
    }
</script>
@endpush