<?php

namespace rymesaint\LaravelOY;

use Illuminate\Support\ServiceProvider;

class LaravelOYServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/config/oy-config.php' => config_path('oy-config.php'),
        ], 'oy-config');

        $this->app->bind('oypayment',function(){
            return new OYPayment();
        });
    }
}
