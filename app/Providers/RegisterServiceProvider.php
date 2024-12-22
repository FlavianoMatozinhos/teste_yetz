<?php

namespace App\Providers;

use App\Repositories\ClasseRepository;
use App\Repositories\PlayerRepository;
use App\Services\RegisterService;
use Illuminate\Support\ServiceProvider;

class RegisterServiceProvider extends ServiceProvider
{
    /**
     * Registra os serviços no container.
     */
    public function register()
    {
        $this->app->singleton(RegisterService::class, function ($app) {
            return new RegisterService(
                $app->make(ClasseRepository::class),
                $app->make(PlayerRepository::class)
            );
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
