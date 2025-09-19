<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Configurar la vista de paginación personalizada
        Paginator::defaultView('vendor.pagination.bootstrap-5');
        Paginator::defaultSimpleView('vendor.pagination.bootstrap-5');
    }
}
