<?php

namespace App\Providers;

use App\Repositories\TokenRepository;
use App\Services\LogoutService;
use Illuminate\Support\ServiceProvider;

class LogoutServiceProvider extends ServiceProvider
{
    /**
     * Registra os serviços no container.
     */
    public function register()
    {
        $this->app->singleton(LogoutService::class, function ($app) {
            return new LogoutService($app->make(TokenRepository::class));
        });
    }

    /**
     * Bootstrap dos serviços.
     */
    public function boot()
    {
        //
    }
}
