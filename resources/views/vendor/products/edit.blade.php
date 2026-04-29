@extends('layouts.app')
@section('title', 'Edit Product — ShopLocal')

@section('content')
<div style="max-width:700px;margin:48px auto;padding:0 24px">

    <div style="margin-bottom:28px">
        <a href="{{ route('vendor.products') }}"
           style="font-size:13px;color:var(--muted);text-decoration:none">← Back to products</a>
        <h1 style="font-family:var(--ff-head);font-size:36px;font-weight:700;margin-top:8px">
            Edit product
        </h1>
    </div>

    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:20px">
        <ul style="margin:0;padding-left:16px">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ✅ EDIT FORM — contains ONLY the update fields --}}
    <form method="POST"
          action="{{ route('vendor.products.update', $product) }}"
          enctype="multipart/form-data"
          id="editForm">
        @csrf
        @method('PUT')

        <div class="form-section">
            <div class="form-section-title">
                <span class="form-step-num">1</span> Basic info
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label>Product name *</label>
                <input type="text" name="name"
                       value="{{ old('name', $product->name) }}" required/>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label>Description *</label>
                <textarea name="description" required rows="4">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->emoji }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Emoji *</label>
                    <input type="text" name="emoji"
                           value="{{ old('emoji', $product->emoji) }}" required maxlength="10"/>
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
                    <input type="number" name="price"
                           value="{{ old('price', $product->price) }}"
                           required min="0" step="0.01"/>
                </div>
                <div class="form-group">
                    <label>Original price (₹)</label>
                    <input type="number" name="old_price"
                           value="{{ old('old_price', $product->old_price) }}"
                           min="0" step="0.01"/>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:14px">
                <label>Stock quantity *</label>
                <input type="number" name="stock"
                       value="{{ old('stock', $product->stock) }}"
                       required min="0" style="max-width:200px"/>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <input type="checkbox" name="is_active" id="is_active"
                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                       style="width:16px;height:16px"/>
                <label for="is_active" style="font-size:14px;cursor:pointer">
                    Active — visible on storefront
                </label>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">
                <span class="form-step-num">3</span> Product image
            </div>

            {{-- Show current image --}}
            @if($product->image)
            <div style="margin-bottom:14px">
                <div style="font-size:13px;color:var(--muted);margin-bottom:8px">Current image:</div>
                <img src="{{ asset('storage/' . $product->image) }}"
                     style="width:100px;height:100px;object-fit:cover;border-radius:10px;border:1px solid var(--border)"/>
            </div>
            @endif

            <div class="form-group">
                <label>Upload new image <span style="color:var(--muted)">(leave blank to keep current)</span></label>
                <input type="file" name="image" accept="image/*"
                       onchange="previewImage(this)"
                       style="padding:8px 0"/>
            </div>
            <div id="imagePreview" style="display:none;margin-top:12px">
                <img id="previewImg"
                     style="width:100px;height:100px;object-fit:cover;border-radius:10px"/>
            </div>
        </div>

        {{-- ✅ Update + Cancel buttons INSIDE edit form --}}
        <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
            <button type="submit" class="btn btn-warm">Update product</button>
            <a href="{{ route('vendor.products') }}" class="btn btn-outline">Cancel</a>
        </div>

    </form>
    {{-- ✅ EDIT FORM ENDS HERE --}}

    {{-- ✅ DELETE FORM — completely separate, OUTSIDE the edit form --}}
    <form method="POST"
          action="{{ route('vendor.products.delete', $product) }}"
          id="deleteForm"
          style="margin-top:16px"
          onsubmit="return confirm('Delete this product? This cannot be undone.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete product</button>
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