<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Apply a coupon code to the cart
     * Called via AJAX from cart page
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ]);
        }

        // Calculate cart subtotal
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        // Find the coupon
        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ]);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has expired or is no longer valid.',
            ]);
        }

        if ($subtotal < $coupon->min_order) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order of ₹' . number_format($coupon->min_order) . ' required for this coupon.',
            ]);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($subtotal);

        // Save coupon to session
        session()->put('coupon', [
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => $coupon->value,
            'discount' => $discount,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => '🎉 Coupon "' . $coupon->code . '" applied!',
            'discount' => number_format($discount, 2),
            'code'     => $coupon->code,
        ]);
    }

    /**
     * Remove applied coupon
     */
    public function remove()
    {
        session()->forget('coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed.',
        ]);
    }
}