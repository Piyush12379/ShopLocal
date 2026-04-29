@extends('layouts.app')
@section('title', 'About Us — ShopLocal')

@section('content')

{{-- Hero --}}
<div style="background:var(--ink);color:var(--paper);padding:80px 48px;text-align:center">
    <div style="max-width:640px;margin:0 auto">
        <div style="font-size:12px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;
                    color:var(--warm);margin-bottom:12px">Our story</div>
        <h1 style="font-family:var(--ff-head);font-size:clamp(36px,5vw,60px);
                   font-weight:700;line-height:1.1;margin-bottom:16px">
            We believe every artisan deserves a <em style="color:var(--warm)">global audience</em>
        </h1>
        <p style="font-size:16px;color:rgba(248,245,239,.6);line-height:1.8;font-weight:300">
            ShopLocal was built to connect talented small business owners with customers
            who appreciate handcrafted, meaningful products.
        </p>
    </div>
</div>

{{-- Mission --}}
<div style="max-width:1100px;margin:0 auto;padding:72px 48px">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center">
        <div>
            <div style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;
                        color:var(--warm);margin-bottom:10px">Our mission</div>
            <h2 style="font-family:var(--ff-head);font-size:40px;font-weight:700;
                       line-height:1.1;margin-bottom:20px">
                Empowering small businesses across India
            </h2>
            <p style="font-size:15px;color:var(--muted);line-height:1.8;margin-bottom:16px">
                Millions of talented artisans across India create beautiful, handcrafted products
                every day — but struggle to reach customers beyond their local area.
            </p>
            <p style="font-size:15px;color:var(--muted);line-height:1.8">
                ShopLocal bridges that gap. We provide a simple, powerful platform for
                shopkeepers to list their products and for customers to discover something
                truly unique — made by real people, with real passion.
            </p>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div style="background:var(--cream);border-radius:16px;padding:24px;text-align:center">
                <div style="font-family:var(--ff-head);font-size:42px;font-weight:700;color:var(--warm)">100+</div>
                <div style="font-size:13px;color:var(--muted);margin-top:4px">Local artisans</div>
            </div>
            <div style="background:var(--cream);border-radius:16px;padding:24px;text-align:center">
                <div style="font-family:var(--ff-head);font-size:42px;font-weight:700;color:var(--warm)">500+</div>
                <div style="font-size:13px;color:var(--muted);margin-top:4px">Happy customers</div>
            </div>
            <div style="background:var(--cream);border-radius:16px;padding:24px;text-align:center">
                <div style="font-family:var(--ff-head);font-size:42px;font-weight:700;color:var(--warm)">6</div>
                <div style="font-size:13px;color:var(--muted);margin-top:4px">Categories</div>
            </div>
            <div style="background:var(--cream);border-radius:16px;padding:24px;text-align:center">
                <div style="font-family:var(--ff-head);font-size:42px;font-weight:700;color:var(--warm)">₹0</div>
                <div style="font-size:13px;color:var(--muted);margin-top:4px">Listing fee</div>
            </div>
        </div>
    </div>
</div>

{{-- Values --}}
<div style="background:var(--cream);padding:72px 48px">
    <div style="max-width:1100px;margin:0 auto">
        <div style="text-align:center;margin-bottom:48px">
            <div style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;
                        color:var(--warm);margin-bottom:10px">What we stand for</div>
            <h2 style="font-family:var(--ff-head);font-size:38px;font-weight:700">Our values</h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px">
            <div style="background:#fff;border-radius:16px;padding:28px;border:1px solid var(--border)">
                <div style="font-size:36px;margin-bottom:16px">🤝</div>
                <h3 style="font-family:var(--ff-head);font-size:22px;font-weight:700;margin-bottom:8px">Community first</h3>
                <p style="font-size:14px;color:var(--muted);line-height:1.7">
                    Every purchase on ShopLocal goes directly to a real artisan.
                    We take zero commission — your money reaches the maker.
                </p>
            </div>
            <div style="background:#fff;border-radius:16px;padding:28px;border:1px solid var(--border)">
                <div style="font-size:36px;margin-bottom:16px">✨</div>
                <h3 style="font-family:var(--ff-head);font-size:22px;font-weight:700;margin-bottom:8px">Quality guaranteed</h3>
                <p style="font-size:14px;color:var(--muted);line-height:1.7">
                    Every vendor on ShopLocal is personally reviewed and approved
                    by our team before they can list products.
                </p>
            </div>
            <div style="background:#fff;border-radius:16px;padding:28px;border:1px solid var(--border)">
                <div style="font-size:36px;margin-bottom:16px">🌿</div>
                <h3 style="font-family:var(--ff-head);font-size:22px;font-weight:700;margin-bottom:8px">Sustainability</h3>
                <p style="font-size:14px;color:var(--muted);line-height:1.7">
                    We prioritize vendors who use sustainable materials and
                    eco-friendly packaging in their products.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- CTA --}}
<div style="text-align:center;padding:72px 48px">
    <h2 style="font-family:var(--ff-head);font-size:40px;font-weight:700;margin-bottom:12px">
        Ready to join ShopLocal?
    </h2>
    <p style="color:var(--muted);font-size:16px;margin-bottom:28px">
        Whether you are a buyer or a seller, there is a place for you here.
    </p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
        <a href="{{ route('register') }}" class="btn btn-primary" style="padding:12px 28px">
            Start selling
        </a>
        <a href="{{ route('home') }}" class="btn btn-outline" style="padding:12px 28px">
            Browse products
        </a>
    </div>
</div>

@endsection