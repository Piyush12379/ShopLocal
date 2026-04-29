<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show the cart page
     */
    public function index()
    {
        // Read cart from session
        // Cart structure: ['product_id' => ['product' => [...], 'quantity' => 2], ...]
        $cart     = session()->get('cart', []);
        $total    = $this->calculateTotal($cart);
        $count    = $this->calculateCount($cart);

        return view('cart.index', compact('cart', 'total', 'count'));
    }

    /**
     * Add a product to cart
     * Called via AJAX from the + button
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Don't allow adding out-of-stock items
        if (!$product->inStock()) {
            return response()->json([
                'success' => false,
                'message' => 'This product is out of stock.',
            ]);
        }

        // Get current cart from session (empty array if nothing there)
        $cart = session()->get('cart', []);

        $id = $product->id;

        if (isset($cart[$id])) {
            // Product already in cart — increase quantity
            // But don't go beyond available stock
            if ($cart[$id]['quantity'] < $product->stock) {
                $cart[$id]['quantity']++;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum available stock.',
                ]);
            }
        } else {
            // Product not in cart yet — add it
            $cart[$id] = [
                'product_id'  => $product->id,
                'name'        => $product->name,
                'price'       => $product->price,
                'emoji'       => $product->emoji,
                'image'       => $product->image,
                'category'    => $product->category->name,
                'stock'       => $product->stock,
                'quantity'    => 1,
            ];
        }

        // Save updated cart back to session
        session()->put('cart', $cart);

        return response()->json([
            'success'    => true,
            'message'    => $product->name . ' added to cart!',
            'cart_count' => $this->calculateCount($cart),
        ]);
    }

    /**
     * Update quantity of a cart item
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity'   => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $id   = $request->product_id;

        if (isset($cart[$id])) {
            // Make sure quantity doesn't exceed stock
            $quantity = min($request->quantity, $cart[$id]['stock']);
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        return response()->json([
            'success'    => true,
            'cart_count' => $this->calculateCount($cart),
            'item_total' => number_format($cart[$id]['price'] * $cart[$id]['quantity'], 2),
            'cart_total' => number_format($this->calculateTotal($cart), 2),
        ]);
    }

    /**
     * Remove an item from cart
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $cart = session()->get('cart', []);
        $id   = $request->product_id;

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'success'    => true,
            'cart_count' => $this->calculateCount($cart),
            'cart_total' => number_format($this->calculateTotal($cart), 2),
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')
                         ->with('success', 'Cart cleared.');
    }

    // ── Private helpers ──────────────────────────────

    private function calculateTotal(array $cart): float
    {
        return collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    private function calculateCount(array $cart): int
    {
        return collect($cart)->sum('quantity');
    }
}