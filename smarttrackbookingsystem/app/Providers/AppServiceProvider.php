<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


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
   public function boot()
{
     View::composer('*', function ($view) {
        if (auth()->check()) {

            $user = auth()->user();

            $business = $user->businessAdmin->business ?? null;
            $subscription = $business ? $business->subscription : null;

            // ✅ pass BOTH
            $view->with('subscription', $subscription);
            $view->with('business', $business);
        }
    });
}
}
