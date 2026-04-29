@extends('layouts.app')
@section('title', 'Contact Us — ShopLocal')

@section('content')

{{-- Header --}}
<div style="background:var(--cream);padding:60px 48px;text-align:center;border-bottom:1px solid var(--border)">
    <div style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;
                color:var(--warm);margin-bottom:10px">Get in touch</div>
    <h1 style="font-family:var(--ff-head);font-size:clamp(32px,5vw,56px);
               font-weight:700;margin-bottom:12px">We would love to hear from you</h1>
    <p style="color:var(--muted);font-size:16px;max-width:480px;margin:0 auto;line-height:1.7">
        Have a question, feedback, or want to partner with us?
        Reach out and our team will get back to you within 24 hours.
    </p>
</div>

<div class="contact-grid">

    {{-- LEFT: Contact info --}}
    <div>
        <h2 style="font-family:var(--ff-head);font-size:32px;font-weight:700;margin-bottom:24px">
            Contact information
        </h2>

        <div class="cd-item">
            <div class="cd-icon">📍</div>
            <div>
                <div class="cd-label">Address</div>
                <div class="cd-val">123 Artisan Lane, Jaipur, Rajasthan — 302001</div>
            </div>
        </div>

        <div class="cd-item">
            <div class="cd-icon">📧</div>
            <div>
                <div class="cd-label">Email</div>
                <div class="cd-val">hello@shoplocal.in</div>
            </div>
        </div>

        <div class="cd-item">
            <div class="cd-icon">📞</div>
            <div>
                <div class="cd-label">Phone</div>
                <div class="cd-val">+91 98765 43210</div>
            </div>
        </div>

        <div class="cd-item">
            <div class="cd-icon">🕐</div>
            <div>
                <div class="cd-label">Business hours</div>
                <div class="cd-val">Monday – Saturday, 9 AM to 6 PM IST</div>
            </div>
        </div>

        {{-- FAQ quick links --}}
        <div style="margin-top:36px;background:var(--cream);border-radius:14px;padding:24px">
            <h3 style="font-size:16px;font-weight:600;margin-bottom:16px">Quick answers</h3>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div style="font-size:14px;padding:10px 0;border-bottom:1px solid var(--border)">
                    <strong>How do I become a vendor?</strong>
                    <p style="color:var(--muted);font-size:13px;margin-top:4px">
                        Register as a Shopkeeper — our team reviews and approves within 24 hours.
                    </p>
                </div>
                <div style="font-size:14px;padding:10px 0;border-bottom:1px solid var(--border)">
                    <strong>What is your return policy?</strong>
                    <p style="color:var(--muted);font-size:13px;margin-top:4px">
                        We offer a 7-day return policy on all products.
                    </p>
                </div>
                <div style="font-size:14px;padding:10px 0">
                    <strong>Is there a listing fee?</strong>
                    <p style="color:var(--muted);font-size:13px;margin-top:4px">
                        No — listing on ShopLocal is completely free for artisans.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Contact form --}}
    <div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:32px">
            <h2 style="font-family:var(--ff-head);font-size:28px;font-weight:700;margin-bottom:24px">
                Send us a message
            </h2>

            @if(session('success'))
                <div class="alert alert-success" style="margin:0 0 20px">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('contact.send') }}">
                @csrf

                <div class="form-grid" style="margin-bottom:14px">
                    <div class="form-group">
                        <label>Your name *</label>
                        <input type="text" name="name"
                               value="{{ old('name', auth()->user()?->name) }}"
                               required placeholder="Full name"/>
                    </div>
                    <div class="form-group">
                        <label>Email address *</label>
                        <input type="email" name="email"
                               value="{{ old('email', auth()->user()?->email) }}"
                               required placeholder="you@example.com"/>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:14px">
                    <label>Subject *</label>
                    <select name="subject" required>
                        <option value="">Select a subject</option>
                        <option value="general"   {{ old('subject') == 'general'   ? 'selected' : '' }}>General enquiry</option>
                        <option value="vendor"    {{ old('subject') == 'vendor'    ? 'selected' : '' }}>Becoming a vendor</option>
                        <option value="order"     {{ old('subject') == 'order'     ? 'selected' : '' }}>Order issue</option>
                        <option value="return"    {{ old('subject') == 'return'    ? 'selected' : '' }}>Return / refund</option>
                        <option value="technical" {{ old('subject') == 'technical' ? 'selected' : '' }}>Technical problem</option>
                        <option value="other"     {{ old('subject') == 'other'     ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:20px">
                    <label>Message *</label>
                    <textarea name="message" required rows="5"
                              placeholder="Tell us how we can help...">{{ old('message') }}</textarea>
                </div>

                @if($errors->any())
                <div class="alert alert-error" style="margin:0 0 16px">
                    <ul style="margin:0;padding-left:16px">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <button type="submit" class="btn btn-primary"
                        style="width:100%;justify-content:center;padding:14px">
                    Send message →
                </button>

                <p style="font-size:12px;color:var(--muted);text-align:center;margin-top:12px">
                    We typically respond within 24 hours on business days.
                </p>
            </form>
        </div>
    </div>

</div>

@endsection