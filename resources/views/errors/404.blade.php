@extends('layouts.app')
@section('title', '404 — Page Not Found')
@section('content')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:40px">
    <div>
        <div style="font-family:var(--ff-head);font-size:120px;font-weight:700;
                    color:var(--cream);line-height:1">404</div>
        <h1 style="font-family:var(--ff-head);font-size:36px;font-weight:700;margin-bottom:12px">
            Page not found
        </h1>
        <p style="color:var(--muted);font-size:16px;margin-bottom:28px">
            The page you are looking for doesn't exist or has been moved.
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary">Go back home</a>
    </div>
</div>
@endsection