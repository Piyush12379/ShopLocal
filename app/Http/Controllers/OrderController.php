<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

// Mailable classes
use App\Mail\OrderConfirmationMail;
use App\Mail\VendorOrderNotificationMail;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('home')
                ->with('error', 'Your cart is empty.');
        }

        // ── NEW: Coupon logic for the checkout view ──────────────
        $total    = $this->calculateTotal($cart);
        $coupon   = session()->get('coupon');
        $discount = $coupon ? $coupon['discount'] : 0;
        $delivery = ($total - $discount) >= 500 ? 0 : 50;
        $grand    = $total - $discount + $delivery;

        return view('checkout.index', compact('cart', 'total', 'coupon', 'discount', 'delivery', 'grand'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'payment_method' => 'required|in:cod,upi,card',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('home')
                ->with('error', 'Your cart is empty.');
        }

        // ── NEW: Apply coupon discount to final order total ──────
        $total    = $this->calculateTotal($cart);
        $coupon   = session()->get('coupon');
        $discount = $coupon ? $coupon['discount'] : 0;
        $delivery = ($total - $discount) >= 500 ? 0 : 50;
        $grand    = $total - $discount + $delivery;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $grand,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'address' => implode(', ', [
                    $request->full_name,
                    $request->phone,
                    $request->address,
                    $request->city,
                    $request->state,
                    $request->pincode,
                ]),
                'coupon_code' => session('coupon.code'),
                'discount'    => session('coupon.discount', 0),
            ]);

            foreach ($cart as $id => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);

                Product::where('id', $id)->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // ── NEW: Increment coupon usage after successful save ──
            if ($coupon) {
                \App\Models\Coupon::where('code', $coupon['code'])->increment('used_count');
                session()->forget('coupon');
            }

            /*
            |------------------------------------------------------
            | STEP 3 — SMART EMAIL SENDING (AFTER COMMIT)
            |------------------------------------------------------
            */

            $fakeDomains = ['shoplocal.com', 'example.com', 'test.com', 'demo.com'];

            $isRealEmail = function ($email) use ($fakeDomains) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return false;
                }

                $domain = strtolower(substr(strrchr($email, '@'), 1));
                return !in_array($domain, $fakeDomains);
            };

            try {
                $order->load('items.product.vendor', 'user');

                // Customer email
                if ($order->user && $isRealEmail($order->user->email)) {
                    Mail::to($order->user->email)
                        ->send(new OrderConfirmationMail($order));
                }

            } catch (\Exception $e) {
                \Log::error('Customer email failed: ' . $e->getMessage());
            }

            try {
                // Vendor emails
                $vendorIds = $order->items
                    ->pluck('product.vendor_id')
                    ->unique()
                    ->filter();

                foreach ($vendorIds as $vendorId) {
                    $vendor = \App\Models\User::find($vendorId);

                    if ($vendor && $isRealEmail($vendor->email)) {
                        Mail::to($vendor->email)
                            ->send(new VendorOrderNotificationMail($order, $vendor));
                    }
                }

            } catch (\Exception $e) {
                \Log::error('Vendor email failed: ' . $e->getMessage());
            }

            session()->forget('cart');

            return redirect()->route('orders.confirmation', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function confirmation($orderId)
    {
        $order = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);

        return view('orders.confirmation', compact('order'));
    }

    public function myOrders()
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    private function calculateTotal(array $cart): float
    {
        return collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function downloadInvoice($id)
    {
        $order = Order::with('items.product.vendor', 'user')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $pdf = Pdf::loadView('orders.invoice', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('ShopLocal-Invoice-' . $order->id . '.pdf');
    }
}