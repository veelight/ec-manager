<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maiev\EC\ECManager;

class ECServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/ec-manager.php' => config_path('ec-manager.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['ec-manager'] = $this->app->share(function ($app) {
            return new ECManager();
        });
    }

    public function provides()
    {
        return ['EC'];
    }
}
