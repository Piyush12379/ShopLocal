<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminApiController extends Controller
{
    /**
     * GET /api/admin/stats
     * Platform statistics
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'total_revenue'   => (float) Order::sum('total_amount'),
                'total_orders'    => Order::count(),
                'total_customers' => User::where('role', 'customer')->count(),
                'total_vendors'   => User::where('role', 'shopkeeper')->where('is_approved', true)->count(),
                'pending_vendors' => User::where('role', 'shopkeeper')->where('is_approved', false)->count(),
                'total_products'  => Product::count(),
            ],
        ]);
    }

    /**
     * GET /api/admin/vendors/pending
     * List pending vendor applications
     */
    public function pendingVendors()
    {
        $vendors = User::where('role', 'shopkeeper')
                       ->where('is_approved', false)
                       ->latest()
                       ->get()
                       ->map(fn($v) => [
                           'id'         => $v->id,
                           'name'       => $v->name,
                           'email'      => $v->email,
                           'applied_on' => $v->created_at->format('d M Y'),
                       ]);

        return response()->json([
            'success' => true,
            'count'   => $vendors->count(),
            'data'    => $vendors,
        ]);
    }

    /**
     * POST /api/admin/vendors/{id}/approve
     * Approve a vendor
     */
    public function approveVendor(User $user)
    {
        if ($user->role !== 'shopkeeper') {
            return response()->json(['success' => false, 'message' => 'User is not a shopkeeper'], 422);
        }

        $user->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => $user->name . ' has been approved as a vendor',
        ]);
    }

    /**
     * GET /api/admin/orders
     * All orders on the platform
     */
    public function orders()
    {
        $orders = Order::with('user')
                       ->latest()
                       ->get()
                       ->map(fn($o) => [
                           'id'             => $o->id,
                           'customer'       => $o->user->name,
                           'total_amount'   => (float) $o->total_amount,
                           'status'         => $o->status,
                           'payment_method' => $o->payment_method,
                           'placed_on'      => $o->created_at->format('d M Y'),
                       ]);

        return response()->json([
            'success' => true,
            'count'   => $orders->count(),
            'data'    => $orders,
        ]);
    }
}