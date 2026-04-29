@extends('layouts.app')
@section('title', 'Awaiting Approval')

@section('content')
<div style="min-height:70vh; display:flex; align-items:center; justify-content:center; text-align:center; padding:40px;">
    <div>
        <div style="font-size:64px; margin-bottom:20px;">⏳</div>
        <h1 style="font-family:var(--ff-head); font-size:40px; margin-bottom:12px;">
            Your application is under review
        </h1>
        <p style="color:var(--muted); font-size:16px; max-width:480px; margin:0 auto 24px;">
            Thank you for registering as a shopkeeper on ShopLocal.
            Our admin team will review your application and approve your account shortly.
        </p>
        <p style="color:var(--muted); font-size:14px;">
            Logged in as: <strong>{{ auth()->user()->email }}</strong>
        </p>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:20px;">
            @csrf
            <button type="submit" class="btn btn-outline">Sign out</button>
        </form>
    </div>
</div>
@endsection