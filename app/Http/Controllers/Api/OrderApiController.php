<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderApiController extends Controller
{
    /**
     * GET /api/orders
     * Customer sees their own orders
     */
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
                       ->where('user_id', $request->user()->id)
                       ->latest()
                       ->get()
                       ->map(fn($o) => $this->formatOrder($o));

        return response()->json([
            'success' => true,
            'count'   => $orders->count(),
            'data'    => $orders,
        ]);
    }

    /**
     * POST /api/orders
     * Place a new order
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'address'        => 'required|string',
            'payment_method' => 'required|in:cod,upi,card',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;

            // Calculate total and verify stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock for: ' . $product->name,
                    ], 422);
                }
                $total += $product->price * $item['quantity'];
            }

            $delivery = $total >= 500 ? 0 : 50;

            // Create order
            $order = Order::create([
                'user_id'        => $request->user()->id,
                'total_amount'   => $total + $delivery,
                'status'         => 'pending',
                'payment_method' => $request->payment_method,
                'address'        => $request->address,
            ]);

            // Create order items + reduce stock
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data'    => $this->formatOrder($order->load('items.product')),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * GET /api/orders/{id}
     * Get single order details
     */
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'data'    => $this->formatOrder($order),
        ]);
    }

    // ── Private helper ────────────────────────────────
    private function formatOrder(Order $o): array
    {
        return [
            'id'             => $o->id,
            'total_amount'   => (float) $o->total_amount,
            'status'         => $o->status,
            'status_emoji'   => $o->statusEmoji(),
            'payment_method' => $o->payment_method,
            'address'        => $o->address,
            'placed_on'      => $o->created_at->format('d M Y'),
            'items'          => $o->items->map(fn($i) => [
                'product'    => $i->product->name ?? 'Deleted product',
                'emoji'      => $i->product->emoji ?? '📦',
                'quantity'   => $i->quantity,
                'unit_price' => (float) $i->unit_price,
                'subtotal'   => (float) $i->subtotal(),
            ]),
        ];
    }
}