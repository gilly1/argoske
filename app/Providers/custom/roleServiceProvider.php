<?php

namespace App\Providers\custom;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class roleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('hasrole',function($role){
            if(Auth::user()->hasAnyRole($role)){
                return true;
            }
            return false;
        });
        Blade::if('hasroles',function(...$roles){
            foreach($roles as $role){
                if(Auth::user()->hasAnyRole($role)){
                    return true;
                }
            }
            return false;
        });
    }
}
