<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Department;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('asset_assignments.*', function ($view) {
            $view->with('departments', Department::select('id', 'areanom')->orderBy('areanom')->get());
        });

        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }

}

