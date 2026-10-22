<?php

namespace App\Providers;

use App\Models\Transaction;
use App\Observers\AccountObserver;
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
       Transaction::observe(AccountObserver::class);
    }
}
