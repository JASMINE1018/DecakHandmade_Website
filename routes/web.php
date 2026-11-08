<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomizationOptionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\VariantController;
use Illuminate\Support\Facades\Route;

// Route::redirect('/', '/admin');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth:admin', 'admin.active'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::resource('products', ProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{index}', [ProductController::class, 'destroyImage'])
            ->whereNumber('index')->name('products.images.destroy');
        Route::post('products/{product}/stock-adjustments', [StockAdjustmentController::class, 'store'])
            ->name('products.stock-adjustments.store');

        Route::scopeBindings()->group(function () {
            Route::post('products/{product}/variants', [VariantController::class, 'store'])->name('products.variants.store');
            Route::put('products/{product}/variants/{variant}', [VariantController::class, 'update'])->name('products.variants.update');
            Route::delete('products/{product}/variants/{variant}', [VariantController::class, 'destroy'])->name('products.variants.destroy');

            Route::post('products/{product}/customization-options', [CustomizationOptionController::class, 'store'])->name('products.customization-options.store');
            Route::put('products/{product}/customization-options/{option}', [CustomizationOptionController::class, 'update'])->name('products.customization-options.update');
            Route::delete('products/{product}/customization-options/{option}', [CustomizationOptionController::class, 'destroy'])->name('products.customization-options.destroy');
        });

        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales.index');
        Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products.index');
        Route::get('reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');

        Route::resource('banners', BannerController::class)->except(['show']);
        Route::patch('banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
        Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
        Route::put('admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
        Route::delete('admins/{admin}', [AdminController::class, 'deactivate'])->name('admins.destroy');
    });
});

use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\AddressController as UserAddressController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\WishlistController as UserWishlistController;
use App\Http\Controllers\User\PasswordController as UserPasswordController;
use App\Http\Controllers\User\AuthController as UserAuthController;

/*
|--------------------------------------------------------------------------
| User Dashboard & Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->group(function () {
    // Auth Routes
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [UserAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [UserAuthController::class, 'login'])->name('login.store');
        Route::get('/register', [UserAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [UserAuthController::class, 'register'])->name('register.store');
    });

    // Public Dashboard / Home (Accessible by guests & customers)
    Route::get('/', [UserProfileController::class, 'index'])->name('dashboard');

    // Protected Dashboard Routes (Protected by Sanctum / Customer Auth)
    Route::middleware('auth:sanctum,customer')->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

        // Profile update
        Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');

        // Addresses
        Route::get('/addresses', [UserAddressController::class, 'index'])->name('addresses');
        Route::post('/addresses', [UserAddressController::class, 'store'])->name('addresses.store');
        Route::put('/addresses/{address}', [UserAddressController::class, 'update'])->name('addresses.update');
        Route::patch('/addresses/{address}/primary', [UserAddressController::class, 'setPrimary'])->name('addresses.primary');
        Route::delete('/addresses/{address}', [UserAddressController::class, 'destroy'])->name('addresses.destroy');

        // Orders
        Route::get('/orders', [UserOrderController::class, 'index'])->name('orders');

        // Wishlist
        Route::get('/wishlist', [UserWishlistController::class, 'index'])->name('wishlist');
        Route::post('/wishlist/{product}/toggle', [UserWishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::delete('/wishlist/{product}', [UserWishlistController::class, 'destroy'])->name('wishlist.destroy');

        // Password
        Route::get('/password', [UserPasswordController::class, 'edit'])->name('password');
        Route::put('/password', [UserPasswordController::class, 'update'])->name('password.update');

        // Cart & Checkout (Interactive simulation)
        Route::get('/cart', function () {
            return view('dashboard-user.cart');
        })->name('cart');

        Route::get('/checkout', function () {
            return view('dashboard-user.checkout');
        })->name('checkout');
    });
});
