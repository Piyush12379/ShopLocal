<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Show the main storefront homepage
     * This powers your home.blade.php
     */
public function index(Request $request)
{
    // Load all categories for the filter bar
    $categories = Category::withCount('products')->get();

    // Start building the product query
    $query = Product::with('category')
                    ->where('is_active', true);

    // If a category filter is selected, apply it
    if ($request->filled('category')) {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('slug', $request->category);
        });
    }

    // If a search term is entered
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Get the products — ordered by newest first
    $products = $query->latest()->get();

    // Get featured products for the hero section (on sale ones)
    $featuredProduct = Product::where('is_active', true)
                              ->whereNotNull('old_price')
                              ->first();

    // Get current user's wishlist product IDs for heart button state
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
        'wishlistIds'
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