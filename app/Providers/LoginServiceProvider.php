<?php

namespace App\Providers;

use App\Repositories\PlayerRepository;
use App\Services\LoginService;
use Illuminate\Support\ServiceProvider;

class LoginServiceProvider extends ServiceProvider
{
    /**
     * Registra os serviços no container.
     */
    public function register()
    {
        $this->app->singleton(LoginService::class, function ($app) {
            return new LoginService($app->make(PlayerRepository::class));
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
