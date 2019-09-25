<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('role', function ($role) {
            return backpack_auth()->check() && backpack_user()->role == $role;
        });

//        \Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
//            dump([$query->sql, $query->bindings, $query->time]);
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
