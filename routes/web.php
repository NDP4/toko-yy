<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Models\Product;  // Add this import
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

// Public routes
Route::get('/', [WelcomeController::class, 'index']);
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    // Add other authenticated routes here

    Route::get('products/download-template', function () {
        // Generate template if not exists
        if (!file_exists(storage_path('app/public/templates/product_import_template.xlsx'))) {
            Artisan::call('products:create-template');
        }

        return response()->download(
            storage_path('app/public/templates/product_import_template.xlsx'),
            'product_import_template.xlsx'
        );
    })->name('products.download-template');
});

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');

// Customer dashboard routes
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
});

// Admin routes (using Filament)
Route::prefix('admin')->group(function () {
    // Filament will handle these routes automatically
});

// API routes for products
Route::get('/api/products/{id}/variants', function ($id) {
    $product = Product::with('variants')->findOrFail($id);
    return response()->json($product->variants);
});

Route::get('/test-log', function () {
    Log::info('This is a test log entry');
    return 'Log entry added';
});
