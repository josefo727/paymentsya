<?php

namespace App\Providers;

use App\Models\TransactionResponse;
use App\Observers\TransactionResponseObserver;
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
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        TransactionResponse::observe(TransactionResponseObserver::class);
    }
}
