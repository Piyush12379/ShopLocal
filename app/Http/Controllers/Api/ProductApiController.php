<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * GET /api/products
     * List all active products with optional filters
     */
    public function index(Request $request)
    {
        $query = Product::with('category', 'vendor')
                        ->where('is_active', true);

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) =>
                $q->where('slug', $request->category)
            );
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        if ($request->filled('sort')) {
            match($request->sort) {
                'price_asc'  => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                'newest'     => $query->latest(),
                default      => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $products = $query->get()->map(fn($p) => $this->formatProduct($p));

        return response()->json([
            'success' => true,
            'count'   => $products->count(),
            'data'    => $products,
        ]);
    }

    /**
     * GET /api/products/{id}
     * Get a single product
     */
    public function show(Product $product)
    {
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->load('category', 'vendor', 'reviews.user');

        return response()->json([
            'success' => true,
            'data'    => [
                ...$this->formatProduct($product),
                'description' => $product->description,
                'vendor'      => $product->vendor->name,
                'reviews'     => $product->reviews->map(fn($r) => [
                    'user'    => $r->user->name,
                    'rating'  => $r->rating,
                    'comment' => $r->comment,
                    'date'    => $r->created_at->format('d M Y'),
                ]),
            ],
        ]);
    }

    /**
     * GET /api/categories
     * List all categories
     */
    public function categories()
    {
        $categories = Category::withCount('products')->get()->map(fn($c) => [
            'id'            => $c->id,
            'name'          => $c->name,
            'slug'          => $c->slug,
            'emoji'         => $c->emoji,
            'products_count'=> $c->products_count,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    /**
     * POST /api/vendor/products
     * Vendor adds a new product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'emoji'       => 'required|string|max:10',
        ]);

        $product = Product::create([
            'vendor_id'   => $request->user()->id,
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'old_price'   => $request->old_price ?? null,
            'stock'       => $request->stock,
            'emoji'       => $request->emoji,
            'is_active'   => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data'    => $this->formatProduct($product->load('category')),
        ], 201);
    }

    /**
     * PUT /api/vendor/products/{id}
     * Vendor updates their product
     */
    public function update(Request $request, Product $product)
    {
        // Only the owner can update
        if ($product->vendor_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
        ]);

        $product->update($request->only([
            'name', 'description', 'price', 'old_price',
            'stock', 'category_id', 'emoji', 'is_active',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data'    => $this->formatProduct($product->load('category')),
        ]);
    }

    /**
     * DELETE /api/vendor/products/{id}
     */
    public function destroy(Request $request, Product $product)
    {
        if ($product->vendor_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    // ── Private helper ────────────────────────────────
    private function formatProduct(Product $p): array
    {
        return [
            'id'         => $p->id,
            'name'       => $p->name,
            'price'      => (float) $p->price,
            'old_price'  => $p->old_price ? (float) $p->old_price : null,
            'discount'   => $p->isOnSale() ? $p->discountPercent() . '%' : null,
            'stock'      => $p->stock,
            'in_stock'   => $p->inStock(),
            'emoji'      => $p->emoji,
            'category'   => $p->category->name ?? null,
            'is_on_sale' => $p->isOnSale(),
            'created_at' => $p->created_at->format('d M Y'),
        ];
    }
}