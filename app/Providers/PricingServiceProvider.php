<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PricingService;

class PricingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PricingService::class, function ($app) {
            return new PricingService();
        });
    }

    public function boot(): void
    {
        //
    }
}
