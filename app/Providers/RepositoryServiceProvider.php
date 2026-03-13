<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Bindings\RepositoryBindings;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        foreach (RepositoryBindings::map() as $abstract => $concrete) {
            $this->app->singleton($abstract, $concrete);
        }
    }

    public function boot()
    {
        //
    }
}