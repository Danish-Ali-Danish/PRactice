<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
// User-side Controllers (Frontend)
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\WishlistController;

/*
 * |--------------------------------------------------------------------------
 * | Authentication Routes
 * |--------------------------------------------------------------------------
 */

Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/*
 * |--------------------------------------------------------------------------
 * | Admin Routes (Protected by auth & isAdmin middleware)
 * |--------------------------------------------------------------------------
 */

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::get('/orders-list', [OrderController::class, 'list'])->name('orders.list');
    Route::resource('customers', CustomerController::class);
    Route::get('/customer-list', [CustomerController::class, 'list'])->name('customers.list');
    Route::resource('reports', ReportController::class);
    Route::resource('settings', SettingController::class);
});

/*
 * |--------------------------------------------------------------------------
 * | User Routes (Frontend - Protected by auth & isUser)
 * |--------------------------------------------------------------------------
 */

Route::middleware(['auth', 'isUser'])->group(function () {
    // Home Page
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Products
    Route::get('/products', [UserProductController::class, 'userIndex'])->name('products.index');
    Route::get('/product/{product}', [UserProductController::class, 'show'])->name('products.show');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    // Orders
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [UserOrderController::class, 'downloadInvoice'])->name('orders.invoice');

    // Reviews
    Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Live Search (AJAX)
    Route::get('/search', [UserProductController::class, 'liveSearch'])->name('products.search');

    // Theme Toggle
    Route::post('/theme-toggle', function () {
        session(['theme' => request('theme')]);
        return response()->json(['status' => 'ok']);
    })->name('theme.toggle');
});
