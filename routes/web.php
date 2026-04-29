<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES — anyone can visit
|--------------------------------------------------------------------------
*/
Route::get('/',                   [ProductController::class, 'index'])->name('home');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Static pages
Route::get('/about',   fn() => view('about'))->name('about');
Route::get('/contact', fn() => view('contact'))->name('contact');
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name'    => 'required|string|max:255',
        'email'   => 'required|email',
        'subject' => 'required|string',
        'message' => 'required|string|min:10',
    ]);
    // In a real app you would send an email here using Laravel Mail
    // For now we just flash a success message
    return back()->with('success', 'Thank you ' . $request->name . '! We have received your message and will reply to ' . $request->email . ' within 24 hours.');
})->name('contact.send');

// Auth — guests only
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| CUSTOMER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->group(function () {

    // Cart
    Route::get('/cart',           [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add',      [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update',  [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear',    [CartController::class, 'clear'])->name('cart.clear');

    // Checkout + Orders
    Route::get('/checkout',              [OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/orders/place',         [OrderController::class, 'placeOrder'])->name('orders.place');
    Route::get('/orders',                [OrderController::class, 'myOrders'])->name('orders.index');
    Route::get('/orders/{id}/confirmed', [OrderController::class, 'confirmation'])->name('orders.confirmation');

    // Reviews
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

});

/*
|--------------------------------------------------------------------------
| VENDOR ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:shopkeeper'])
     ->prefix('vendor')->name('vendor.')
     ->group(function () {

    Route::get('/pending', fn() => view('vendor.pending'))->name('pending');

    Route::middleware('approved')->group(function () {
        Route::get('/dashboard',                      [VendorController::class, 'dashboard'])->name('dashboard');
        Route::get('/products',                       [VendorController::class, 'products'])->name('products');
        Route::get('/products/create',                [VendorController::class, 'createProduct'])->name('products.create');
        Route::post('/products',                      [VendorController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{product}/edit',        [VendorController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{product}',             [VendorController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}',          [VendorController::class, 'deleteProduct'])->name('products.delete');
        Route::get('/orders',                         [VendorController::class, 'orders'])->name('orders');
    });

});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')->name('admin.')
     ->group(function () {

    Route::get('/dashboard',                    [AdminController::class, 'dashboard'])->name('dashboard');

    // Vendors
    Route::get('/vendors',                      [AdminController::class, 'vendors'])->name('vendors');
    Route::post('/vendors/{user}/approve',      [AdminController::class, 'approveVendor'])->name('vendors.approve');
    Route::post('/vendors/{user}/reject',       [AdminController::class, 'rejectVendor'])->name('vendors.reject');

    // Users
    Route::get('/users',                        [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{user}',              [AdminController::class, 'deleteUser'])->name('users.delete');

    // Orders
    Route::get('/orders',                       [AdminController::class, 'orders'])->name('orders');
    Route::post('/orders/{order}/status',       [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Products
    Route::get('/products',                     [AdminController::class, 'products'])->name('products');

    // Categories
    Route::get('/categories',                   [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories',                  [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{category}',     [AdminController::class, 'deleteCategory'])->name('categories.delete');

});