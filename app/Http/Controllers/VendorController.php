<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage; // Model import add kiya hai
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    /**
     * Vendor dashboard — stats + recent products
     */
    public function dashboard()
    {
        $vendor = auth()->user();

        // Total revenue from delivered orders
        $totalRevenue = OrderItem::whereHas('order', fn($q) =>
                            $q->where('status', '!=', 'pending')
                        )
                        ->whereHas('product', fn($q) =>
                            $q->where('vendor_id', $vendor->id)
                        )
                        ->get()
                        ->sum(fn($item) => $item->unit_price * $item->quantity);

        // Total products
        $totalProducts = Product::where('vendor_id', $vendor->id)->count();

        // Total orders containing vendor's products
        $totalOrders = OrderItem::whereHas('product', fn($q) =>
                           $q->where('vendor_id', $vendor->id)
                       )->distinct('order_id')->count('order_id');

        // Recent products
        $products = Product::where('vendor_id', $vendor->id)
                           ->with('category')
                           ->latest()
                           ->take(5)
                           ->get();

        // Recent orders
        $recentOrders = OrderItem::with(['order.user', 'product'])
                                 ->whereHas('product', fn($q) =>
                                     $q->where('vendor_id', $vendor->id)
                                 )
                                 ->latest('order_id')
                                 ->take(5)
                                 ->get();

        return view('vendor.dashboard', compact(
            'totalRevenue', 'totalProducts', 'totalOrders',
            'products', 'recentOrders'
        ));
    }

    /**
     * Show all vendor products
     */
    public function products()
    {
        $products = Product::where('vendor_id', auth()->id())
                           ->with('category')
                           ->latest()
                           ->get();

        return view('vendor.products.index', compact('products'));
    }

    /**
     * Show add product form
     */
    public function createProduct()
    {
        $categories = Category::all();
        return view('vendor.products.create', compact('categories'));
    }

    /**
     * Save new product
     */
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'old_price'   => 'nullable|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'emoji'       => 'required|string|max:10',
            // VALIDATION UPDATED: Allow up to 5 images, max 10MB (10240 KB) each
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // 1. Create the product FIRST so we have an ID for the images
        $product = Product::create([
            'vendor_id'   => auth()->id(),
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'old_price'   => $request->old_price ?: null,
            'stock'       => $request->stock,
            'emoji'       => $request->emoji,
            'image'       => null, // Will update below if images exist
            'is_active'   => $request->has('is_active'),
        ]);

        // 2. Handle multiple image uploads
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            $first  = true;
            foreach ($images as $index => $image) {
                $path = $image->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $first,
                    'sort_order' => $index,
                ]);

                if ($first) {
                    // Also save first image to main products.image for backward compatibility
                    $product->update(['image' => $path]);
                    $first = false;
                }
            }
        }

        return redirect()->route('vendor.products')
                         ->with('success', 'Product added successfully!');
    }

    /**
     * Show edit product form
     */
    public function editProduct(Product $product)
    {
        // Make sure vendor can only edit their OWN products
        if ($product->vendor_id !== auth()->id()) {
            abort(403, 'You do not own this product.');
        }

        $categories = Category::all();
        return view('vendor.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function updateProduct(Request $request, Product $product)
    {
        if ($product->vendor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'old_price'   => 'nullable|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'emoji'       => 'required|string|max:10',
            // VALIDATION UPDATED: Allow up to 5 images, max 10MB (10240 KB) each
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // 1. Update basic product details
        $product->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'old_price'   => $request->old_price ?: null,
            'stock'       => $request->stock,
            'emoji'       => $request->emoji,
            'is_active'   => $request->has('is_active'),
        ]);

        // 2. Handle new image uploads
        if ($request->hasFile('images')) {
            // Delete old images from storage
            foreach ($product->images as $oldImg) {
                Storage::disk('public')->delete($oldImg->image_path);
            }
            // Delete old images from database
            $product->images()->delete();
            
            $first = true;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $first,
                    'sort_order' => $index,
                ]);

                if ($first) {
                    $product->update(['image' => $path]);
                    $first = false;
                }
            }
        }

        return redirect()->route('vendor.products')
                         ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product
     */
    public function deleteProduct(Product $product)
    {
        if ($product->vendor_id !== auth()->id()) {
            abort(403);
        }

        // Delete all gallery images from storage
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        
        // Delete main image fallback
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('vendor.products')
                         ->with('success', 'Product deleted.');
    }

    /**
     * Vendor orders page
     */
    public function orders()
    {
        $orderItems = OrderItem::with(['order.user', 'product'])
                               ->whereHas('product', fn($q) =>
                                   $q->where('vendor_id', auth()->id())
                               )
                               ->latest('order_id')
                               ->get()
                               ->groupBy('order_id');

        return view('vendor.orders', compact('orderItems'));
    }
}