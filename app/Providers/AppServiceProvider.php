<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CompanyService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CompanyService::class, function ($app) {
            return new CompanyService(env('RAPIDAPI_KEY'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
