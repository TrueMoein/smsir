<?php

namespace phplusir\smsir;

use Illuminate\Support\ServiceProvider;

class SmsirServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
	    $this->loadRoutesFrom(__DIR__.'/routes.php');
	    $this->loadViewsFrom(__DIR__.'/views', 'smsir');
	    $this->loadMigrationsFrom(__DIR__.'/migrations');



	    $this->publishes([
		    __DIR__.'/views' => resource_path('views/vendor/smsir'),
	    ]);

	    $this->publishes([
		    __DIR__.'/assets' => public_path('vendor/smsir'),
	    ], 'public');

	    $this->publishes([
		    __DIR__.'/config/smsir.php' => config_path('smsir.php'),
	    ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
	    $this->mergeConfigFrom(
		    __DIR__.'/config/smsir.php', 'smsir'
	    );
    }
}
