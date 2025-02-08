<?php

namespace MultiversX\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use MultiversX\Laravel\Services\SmartContractService;
use MultiversX\Laravel\Services\WalletService;
use MultiversX\Laravel\Services\MarketplaceService;
use MultiversX\Laravel\Services\DynamicNftService;

class MultiversXServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/multiversx.php', 'multiversx'
        );

        // Register services
        $this->app->singleton(SmartContractService::class, function ($app) {
            return new SmartContractService($app['config']['multiversx']);
        });

        $this->app->singleton(WalletService::class, function ($app) {
            return new WalletService($app['config']['multiversx']);
        });

        $this->app->singleton(MarketplaceService::class, function ($app) {
            return new MarketplaceService($app['config']['multiversx']);
        });

        $this->app->singleton(DynamicNftService::class, function ($app) {
            return new DynamicNftService($app['config']['multiversx']);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../../config/multiversx.php' => config_path('multiversx.php'),
        ], 'multiversx-config');
    }
}
