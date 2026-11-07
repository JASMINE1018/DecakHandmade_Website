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

/*
|--------------------------------------------------------------------------
| User Dashboard Routes (Frontend Only)
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->group(function () {
    Route::get('/', function () {
        return view('dashboard-user.profile');
    })->name('dashboard');

    Route::get('/addresses', function () {
        return view('dashboard-user.addresses');
    })->name('addresses');

    Route::get('/orders', function () {
        return view('dashboard-user.orders');
    })->name('orders');

    Route::get('/wishlist', function () {
        return view('dashboard-user.wishlist');
    })->name('wishlist');

    Route::get('/password', function () {
        return view('dashboard-user.password');
    })->name('password');

    Route::get('/cart', function () {
        return view('dashboard-user.cart');
    })->name('cart');

    Route::get('/checkout', function () {
        return view('dashboard-user.checkout');
    })->name('checkout');
});
