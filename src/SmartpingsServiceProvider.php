<?php

namespace Smartpings\Messaging;

use Illuminate\Support\ServiceProvider;

class SmartpingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/smartpings.php' => config_path('smartpings.php'),
            ], 'config');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/smartpings.php', 'smartpings');

        $this->app->singleton(SmartpingsService::class, function ($app) {
            $config = $app['config']['smartpings'];

            return SmartpingsService::create(
                $config['client_id'],
                $config['secret_id'],
                $config['api_url']
            );
        });
    }
}
