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
                <span class="form-step-num">3</span> Product images
            </div>

            <div class="form-group">
                <label>
                    Upload images
                    <span style="color:var(--muted)">(up to 5 images — first one is the main image)</span>
                </label>
                {{-- multiple attribute allows selecting multiple files --}}
                <input type="file"
                       name="images[]"
                       accept="image/*"
                       multiple
                       onchange="previewImages(this)"
                       style="padding:8px 0"/>
            </div>

            {{-- Image previews grid --}}
            <div id="imagePreviews"
                 style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-top:10px">
            </div>

            <div style="margin-top:8px;font-size:12px;color:var(--muted)">
                JPG, PNG, WebP — max 10MB each. First image selected becomes the main display image.
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
// We use DataTransfer to act as our "shopping cart" for files
let selectedFiles = new DataTransfer();

function previewImages(input) {
    const container = document.getElementById('imagePreviews');

    // 1. Add newly selected files to our "cart" (up to 5 maximum)
    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            if (selectedFiles.items.length < 5) {
                selectedFiles.items.add(input.files[i]);
            } else {
                alert("You can only upload a maximum of 5 images.");
                break; // Stop adding if we hit the limit
            }
        }
    }

    // 2. Sync the hidden input with our cart so the backend gets all the files
    input.files = selectedFiles.files;

    // 3. Clear the visual preview container to rebuild it
    container.innerHTML = '';

    // 4. Build the thumbnails
    Array.from(selectedFiles.files).forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = e => {
            const wrapper = document.createElement('div');
            wrapper.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;aspect-ratio:1;border:1px solid var(--border)';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover';

            // Add the "Main" badge to the first image
            if (index === 0) {
                const badge = document.createElement('div');
                badge.textContent = 'Main';
                badge.style.cssText = 'position:absolute;bottom:4px;left:4px;background:#D4A853;color:#fff;font-size:9px;padding:1px 5px;border-radius:3px;font-weight:600;z-index:2;';
                wrapper.appendChild(badge);
            }

            // Add a handy "Remove (x)" button to each thumbnail
            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '×';
            removeBtn.type = 'button';
            removeBtn.style.cssText = 'position:absolute;top:4px;right:4px;background:rgba(0,0,0,0.6);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:14px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:2;';
            
            // What happens when you click remove
            removeBtn.onclick = function() {
                removeFile(index, input);
            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            container.appendChild(wrapper);
        };

        reader.readAsDataURL(file);
    });
}

// Function to remove a specific file from our "cart"
function removeFile(indexToRemove, inputElement) {
    const newCart = new DataTransfer();
    
    // Copy everything over EXCEPT the one we want to remove
    for (let i = 0; i < selectedFiles.items.length; i++) {
        if (i !== indexToRemove) {
            newCart.items.add(selectedFiles.files[i]);
        }
    }
    
    // Update our main cart and the input
    selectedFiles = newCart;
    inputElement.files = selectedFiles.files;
    
    // Re-render the previews
    previewImages(inputElement); 
}
</script>
@endpush