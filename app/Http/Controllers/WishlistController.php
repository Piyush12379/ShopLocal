<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Show the wishlist page
     */
    public function index()
    {
        $wishlistItems = Wishlist::with('product.category')
                                 ->where('user_id', auth()->id())
                                 ->latest()
                                 ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    /**
     * Toggle product in wishlist (add if not there, remove if already there)
     * Called via AJAX
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $existing = Wishlist::where('user_id', auth()->id())
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($existing) {
            // Already in wishlist — remove it
            $existing->delete();
            $inWishlist = false;
            $message    = 'Removed from wishlist';
        } else {
            // Not in wishlist — add it
            Wishlist::create([
                'user_id'    => auth()->id(),
                'product_id' => $request->product_id,
            ]);
            $inWishlist = true;
            $message    = 'Added to wishlist!';
        }

        // Count total wishlist items
        $count = Wishlist::where('user_id', auth()->id())->count();

        return response()->json([
            'success'     => true,
            'in_wishlist' => $inWishlist,
            'message'     => $message,
            'count'       => $count,
        ]);
    }

    /**
     * Remove a specific item from wishlist
     */
    public function remove(Request $request)
    {
        Wishlist::where('user_id', auth()->id())
                ->where('product_id', $request->product_id)
                ->delete();

        return back()->with('success', 'Removed from wishlist.');
    }
}