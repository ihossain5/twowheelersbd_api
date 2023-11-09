<?php

namespace App\Providers;

use App\Classes\ColorUtility;
use Illuminate\Support\ServiceProvider;

class ColorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('color-utility', function () {
            return new ColorUtility;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
