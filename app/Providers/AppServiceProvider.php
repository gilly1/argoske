<?php

namespace App\Providers;

use App\Http\Controllers\dbLoopService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('dbsave', function($app){
            return new dbLoopService();
        });
    }
}
