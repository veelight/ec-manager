<?php

namespace Maiev\EC\Providers;

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
            __DIR__ . '/../Config/ec-manager.php' => config_path('ec-manager.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('EC', function ($app) {
            return new ECManager();
        });
    }

}
