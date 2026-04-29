@extends('layouts.app')
@section('title', 'Login — ShopLocal')

@section('content')
<div style="min-height:80vh; display:flex; align-items:center; justify-content:center; padding:40px 20px;">
    <div style="width:100%; max-width:420px;">

        <div style="text-align:center; margin-bottom:32px;">
            <h1 style="font-family:var(--ff-head); font-size:36px; font-weight:700;">Welcome back</h1>
            <p style="color:var(--muted); margin-top:8px;">Sign in to your ShopLocal account</p>
        </div>

        {{-- Show validation errors --}}
        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:16px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf  {{-- Security token — ALWAYS required in forms --}}

            <div class="form-group" style="margin-bottom:16px;">
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Email address</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="you@example.com"
                    style="width:100%; padding:12px 16px; border:1px solid var(--border); border-radius:var(--r); font-family:var(--ff-body); font-size:15px;"
                />
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label style="display:block; font-size:14px; font-weight:500; margin-bottom:6px;">Password</label>
                <input
                    type="password"
                    name="password"
                    required
                    placeholder="Your password"
                    style="width:100%; padding:12px 16px; border:1px solid var(--border); border-radius:var(--r); font-family:var(--ff-body); font-size:15px;"
                />
            </div>

            <div style="display:flex; align-items:center; gap:8px; margin-bottom:24px;">
                <input type="checkbox" name="remember" id="remember" style="width:16px;height:16px;">
                <label for="remember" style="font-size:14px; color:var(--muted);">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:14px;">
                Sign in
            </button>
        </form>

        <p style="text-align:center; margin-top:20px; font-size:14px; color:var(--muted);">
            Don't have an account?
            <a href="{{ route('register') }}" style="color:var(--warm); font-weight:500;">Register here</a>
        </p>
    </div>
</div>
@endsection