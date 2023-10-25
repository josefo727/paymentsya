<?php

namespace App\Providers;

use App\Models\TransactionResponse;
use App\Observers\TransactionResponseObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Database\Events\MigrationsStarted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
		if (env('ALLOW_DISABLED_PK', false)) {
			$this->allowDisabledPk();
		}
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

	private function allowDisabledPk(): void
	{
		Event::listen(MigrationsStarted::class, function (){
			DB::statement('SET SESSION sql_require_primary_key=0');
		});

		Event::listen(MigrationsEnded::class, function (){
			DB::statement('SET SESSION sql_require_primary_key=1');
		});
	}
}
