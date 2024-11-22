<?php

namespace App\Providers;

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
        // Filament::serving(function () {
        //     Filament::registerNavigationGroups([
        //         'Subscription Management',
        //         'Website Management', 
        //         'Financial Management',
        //         'User Management'
        //     ]);
        // });
    }
}
