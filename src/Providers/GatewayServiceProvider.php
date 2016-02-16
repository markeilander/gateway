<?php

namespace Eilander\Gateway\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class GatewayServiceProvider
 * @package Eilander\Gateway\Providers
 */
class GatewayServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../Config/Gateway.php' => config_path('gateway.php')
        ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/Gateway.php', 'gateway'
        );

        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'gateway');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
    }
}