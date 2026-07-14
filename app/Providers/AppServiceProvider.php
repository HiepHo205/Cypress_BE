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
    public function boot(): void
    {
        $adminViewsPath = app_path('Modules/Admin/Views');

        if (is_dir($adminViewsPath)) {
            View::addNamespace('admin', $adminViewsPath);
        }
    }
}
