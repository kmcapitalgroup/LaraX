<?php

namespace MultiversX\Laravel;

use Illuminate\Support\ServiceProvider;
use MultiversX\Laravel\Services\MultiversXService;

class MultiversXServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/multiversx.php', 'multiversx'
        );

        $this->app->singleton('multiversx', function ($app) {
            return new MultiversXService(config('multiversx'));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/multiversx.php' => config_path('multiversx.php'),
        ], 'config');
    }
}
