@extends('layouts.app')
@section('title', 'Add Product — ShopLocal')

@section('content')
<div style="max-width:700px;margin:48px auto;padding:0 24px">

    <div style="margin-bottom:28px">
        <a href="{{ route('vendor.products') }}"
           style="font-size:13px;color:var(--muted);text-decoration:none">← Back to products</a>
        <h1 style="font-family:var(--ff-head);font-size:36px;font-weight:700;margin-top:8px">Add new product</h1>
    </div>

    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:20px">
        <ul style="margin:0;padding-left:16px">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST"
          action="{{ route('vendor.products.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="form-section">
            <div class="form-section-title">
                <span class="form-step-num">1</span> Basic info
            </div>

            <div class="form-group" style="margin-bottom:14px">
                <label>Product name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       required placeholder="e.g. Terracotta Planter Set"/>
            </div>

            <div class="form-group" style="margin-bottom:14px">
                <label>Description *</label>
                <textarea name="description" required rows="4"
                          placeholder="Describe your product in detail...">{{ old('description') }}</textarea>
            </div>

            <div class="form-grid" style="margin-bottom:14px">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->emoji }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Emoji (for display) *</label>
                    <input type="text" name="emoji" value="{{ old('emoji', '📦') }}"
                           required placeholder="📦" maxlength="10"/>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">
                <span class="form-step-num">2</span> Pricing & stock
            </div>

            <div class="form-grid" style="margin-bottom:14px">
                <div class="form-group">
                    <label>Price (₹) *</label>
                    <input type="number" name="price" value="{{ old('price') }}"
                           required min="0" step="0.01" placeholder="849"/>
                </div>
                <div class="form-group">
                    <label>Original price (₹) <span style="color:var(--muted)">(optional — shows strikethrough)</span></label>
                    <input type="number" name="old_price" value="{{ old('old_price') }}"
                           min="0" step="0.01" placeholder="1099"/>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:14px">
                <label>Stock quantity *</label>
                <input type="number" name="stock" value="{{ old('stock', 1) }}"
                       required min="0" placeholder="10" style="max-width:200px"/>
            </div>

            <div style="display:flex;align-items:center;gap:10px">
                <input type="checkbox" name="is_active" id="is_active"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       style="width:16px;height:16px"/>
                <label for="is_active" style="font-size:14px;cursor:pointer">
                    Active — visible to customers on the storefront
                </label>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">
                <span class="form-step-num">3</span> Product image
            </div>

            <div class="form-group">
                <label>Upload image <span style="color:var(--muted)">(optional — max 2MB, JPG/PNG/WebP)</span></label>
                <input type="file" name="image" accept="image/*"
                       onchange="previewImage(this)"
                       style="padding:8px 0"/>
            </div>

            <div id="imagePreview" style="display:none;margin-top:12px">
                <img id="previewImg"
                     style="width:120px;height:120px;object-fit:cover;border-radius:10px;border:1px solid var(--border)"/>
            </div>

            <div style="margin-top:10px;font-size:13px;color:var(--muted)">
                If no image is uploaded, the emoji will be shown instead.
            </div>
        </div>

        <div style="display:flex;gap:12px">
            <button type="submit" class="btn btn-warm">Save product</button>
            <a href="{{ route('vendor.products') }}" class="btn btn-outline">Cancel</a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const img     = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush