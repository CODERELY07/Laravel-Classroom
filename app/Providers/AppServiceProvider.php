<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
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
        // Numeric route keys only, so malformed URLs 404 instead of hitting the database.
        Route::patterns([
            'class' => '[0-9]+',
            'material' => '[0-9]+',
            'question' => '[0-9]+',
            'student' => '[0-9]+',
        ]);
    }
}
