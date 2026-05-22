<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Show the main storefront homepage
     * Includes filtering, searching, and sorting
     */
    public function index(Request $request)
    {
        // Load all categories for the filter bar
        $categories = Category::withCount('products')->get();

        // Start building the product query
        $query = Product::with('category')
                        ->where('is_active', true);

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Price range filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // In-stock filter
        if ($request->filled('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Sorting logic
        match($request->get('sort', 'newest')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->latest(),
        };

        $products = $query->get();

        // Stats for the UI filter placeholders
        $priceStats = Product::where('is_active', true)
                             ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                             ->first();

        // Featured product for hero
        $featuredProduct = Product::where('is_active', true)
                                  ->whereNotNull('old_price')
                                  ->first();

        // Wishlist IDs
        $wishlistIds = [];
        if (auth()->check() && auth()->user()->isCustomer()) {
            $wishlistIds = \App\Models\Wishlist::where('user_id', auth()->id())
                                               ->pluck('product_id')
                                               ->toArray();
        }

        return view('home', compact(
            'products', 
            'categories', 
            'featuredProduct', 
            'wishlistIds',
            'priceStats'
        ));
    }

    /**
     * Show a single product detail page
     */
    public function show(Product $product)
    {
        // Load related data
        $product->load('category', 'vendor', 'reviews.user');

        // Related products from same category
        $related = Product::where('category_id', $product->category_id)
                          ->where('id', '!=', $product->id)
                          ->where('is_active', true)
                          ->take(4)
                          ->get();

        return view('products.show', compact('product', 'related'));
    }
}