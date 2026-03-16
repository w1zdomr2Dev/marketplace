<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;


// ═══════════════════════════════════════════
// PUBLIC ROUTES — kahit sino, walang login
// ═══════════════════════════════════════════

// Homepage — lahat ng products
Route::get('/', [ProductController::class, 'index']);

// View single product
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Search products
Route::get('/search', [ProductController::class, 'search']);

// ═══════════════════════════════════════════
// BUYER ROUTES — kailangan naka-login + buyer
// ═══════════════════════════════════════════
Route::middleware(['auth', 'buyer'])->group(function () {
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Favorites
    Route::post('/favorites/{product}', [FavoriteController::class, 'toggle']);
});

// ═══════════════════════════════════════════
// SELLER ROUTES — kailangan naka-login + seller
// ═══════════════════════════════════════════
Route::middleware(['auth', 'seller'])->group(function () {
    // Seller Dashboard
    Route::get('/seller/dashboard', [SellerController::class, 'dashboard']);

    // Product Management
    Route::get('/seller/products', [ProductController::class, 'sellerIndex']);
    Route::get('/seller/products/create', [ProductController::class, 'create']);
    Route::post('/seller/products', [ProductController::class, 'store']);
    Route::get('/seller/products/{id}/edit', [ProductController::class, 'edit']);
    Route::patch('/seller/products/{id}', [ProductController::class, 'update']);
    Route::delete('/seller/products/{id}', [ProductController::class, 'destroy']);

    // Order Management
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});

// ═══════════════════════════════════════════
// ADMIN ROUTES — kailangan naka-login + admin
// ═══════════════════════════════════════════
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);

    // User Management
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::patch('/admin/users/{id}/ban', [AdminController::class, 'ban']);

    // Listing Management
    Route::delete('/admin/products/{id}', [AdminController::class, 'removeProduct']);

    // Activity Logs
    Route::get('/admin/logs', [AdminController::class, 'logs']);
});

// ═══════════════════════════════════════════
// PROFILE ROUTES — Breeze default
// ═══════════════════════════════════════════
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';