<?php

namespace App\Providers;

use App\Repositories\ClassRepository;
use App\Services\ClassService;
use Illuminate\Support\ServiceProvider;

class ClassServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ClassService::class, function ($app) {
            return new ClassService(new ClassRepository());
        });
    }

    public function boot()
    {
        //
    }
}
