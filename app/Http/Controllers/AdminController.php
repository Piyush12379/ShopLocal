<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin dashboard — platform overview
     */
    public function dashboard()
    {
        $totalRevenue   = Order::where('status', '!=', 'pending')->sum('total_amount');
        $totalOrders    = Order::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalVendors   = User::where('role', 'shopkeeper')->where('is_approved', true)->count();
        $pendingVendors = User::where('role', 'shopkeeper')->where('is_approved', false)->count();
        $totalProducts  = Product::count();

        // Recent orders
        $recentOrders = Order::with('user')
                             ->latest()
                             ->take(8)
                             ->get();

        // Pending vendor applications
        $pendingVendorList = User::where('role', 'shopkeeper')
                                 ->where('is_approved', false)
                                 ->latest()
                                 ->take(5)
                                 ->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'totalCustomers',
            'totalVendors', 'pendingVendors', 'totalProducts',
            'recentOrders', 'pendingVendorList'
        ));
    }

    /**
     * All vendors — with approve/reject actions
     */
    public function vendors()
    {
        $pendingVendors  = User::where('role', 'shopkeeper')
                               ->where('is_approved', false)
                               ->latest()->get();

        $approvedVendors = User::where('role', 'shopkeeper')
                               ->where('is_approved', true)
                               ->withCount('products')
                               ->latest()->get();

        return view('admin.vendors', compact('pendingVendors', 'approvedVendors'));
    }

    /**
     * Approve a vendor
     */
    public function approveVendor(User $user)
    {
        $user->update(['is_approved' => true]);

        return back()->with('success', $user->name . ' has been approved as a vendor!');
    }

    /**
     * Reject / suspend a vendor
     */
    public function rejectVendor(User $user)
    {
        $user->update(['is_approved' => false]);

        return back()->with('success', $user->name . ' has been rejected.');
    }

    /**
     * All users
     */
    public function users()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    /**
     * All orders
     */
    public function orders()
    {
        $orders = Order::with('user')
                       ->latest()
                       ->get();

        return view('admin.orders', compact('orders'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order #' . $order->id . ' status updated to ' . $request->status);
    }

    /**
     * All products
     */
    public function products()
    {
        $products = Product::with(['vendor', 'category'])->latest()->get();
        return view('admin.products', compact('products'));
    }

    /**
     * Categories list
     */
    public function categories()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories', compact('categories'));
    }

    /**
     * Add new category
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100|unique:categories,name',
            'emoji' => 'required|string|max:10',
        ]);

        Category::create([
            'name'  => $request->name,
            'slug'  => \Str::slug($request->name),
            'emoji' => $request->emoji,
        ]);

        return back()->with('success', 'Category "' . $request->name . '" added!');
    }

    /**
     * Delete category
     */
    public function deleteCategory(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete — this category has products.');
        }

        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}