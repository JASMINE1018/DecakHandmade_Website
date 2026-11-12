<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        \Illuminate\Support\Facades\View::composer('dashboard-user.*', function ($view) {
            $customer = \Illuminate\Support\Facades\Auth::guard('customer')->user()
                ?? \Illuminate\Support\Facades\Auth::user();
            $view->with('currentUser', $customer);
        });
    }
}
