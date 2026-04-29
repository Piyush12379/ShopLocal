@extends('layouts.app')
@section('title', '500 — Server Error')
@section('content')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:40px">
    <div>
        <div style="font-size:64px;margin-bottom:16px">⚠️</div>
        <h1 style="font-family:var(--ff-head);font-size:36px;font-weight:700;margin-bottom:12px">
            Something went wrong
        </h1>
        <p style="color:var(--muted);font-size:16px;margin-bottom:28px">
            We are working on fixing this. Please try again shortly.
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary">Go back home</a>
    </div>
</div>
@endsection