<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Show the checkout form
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        // If cart is empty, send back to shop
        if (empty($cart)) {
            return redirect()->route('home')
                             ->with('error', 'Your cart is empty.');
        }

        $total    = $this->calculateTotal($cart);
        $delivery = $total >= 500 ? 0 : 50;
        $grand    = $total + $delivery;

        return view('checkout.index', compact('cart', 'total', 'delivery', 'grand'));
    }

    /**
     * Place the order — saves to DB
     */
    public function placeOrder(Request $request)
    {
        // Step 1: Validate the form
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required|string|max:15',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'state'          => 'required|string|max:100',
            'pincode'        => 'required|string|max:10',
            'payment_method' => 'required|in:cod,upi,card',
        ]);

        $cart = session()->get('cart', []);

        // If cart is empty, redirect
        if (empty($cart)) {
            return redirect()->route('home')
                             ->with('error', 'Your cart is empty.');
        }

        $total    = $this->calculateTotal($cart);
        $delivery = $total >= 500 ? 0 : 50;
        $grand    = $total + $delivery;

        // Step 2: Use DB transaction
        // If anything fails, everything rolls back — no partial orders
        DB::beginTransaction();

        try {
            // Step 3: Create the order
            $order = Order::create([
                'user_id'        => auth()->id(),
                'total_amount'   => $grand,
                'status'         => 'pending',
                'payment_method' => $request->payment_method,
                'address'        => implode(', ', [
                    $request->full_name,
                    $request->phone,
                    $request->address,
                    $request->city,
                    $request->state,
                    $request->pincode,
                ]),
            ]);

            // Step 4: Save each cart item as an order item
            foreach ($cart as $id => $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);

                // Step 5: Reduce stock for each product
                Product::where('id', $id)->decrement('stock', $item['quantity']);
            }

            // Step 6: Commit transaction — everything saved successfully
            DB::commit();

            // Step 7: Clear the cart from session
            session()->forget('cart');

            // Step 8: Redirect to confirmation page
            return redirect()->route('orders.confirmation', $order->id)
                             ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            // Something went wrong — rollback everything
            DB::rollBack();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Show order confirmation page
     */
    public function confirmation($orderId)
    {
        $order = Order::with('items.product')
                      ->where('user_id', auth()->id())
                      ->findOrFail($orderId);

        return view('orders.confirmation', compact('order'));
    }

    /**
     * Show all orders for the logged-in customer
     */
    public function myOrders()
    {
        $orders = Order::with('items.product')
                       ->where('user_id', auth()->id())
                       ->latest()
                       ->get();

        return view('orders.index', compact('orders'));
    }

    // ── Private helper ────────────────────────────────
    private function calculateTotal(array $cart): float
    {
        return collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }
}