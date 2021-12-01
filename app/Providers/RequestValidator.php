<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RequestValidator extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(App\MyClasses\RequestValidator::class, function ($app) {
            return new App\MyClasses\RequestValidator();
        });
    }
}