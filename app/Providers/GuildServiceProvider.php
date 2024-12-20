<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\GuildRepository;
use App\Repositories\PlayerRepository;
use App\Services\GuildBalancerService;
use App\Services\ClassBalanceStrategy;
use App\Services\XPBalanceStrategy;

class GuildServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GuildRepository::class, function ($app) {
            return new GuildRepository();
        });

        $this->app->singleton(PlayerRepository::class, function ($app) {
            return new PlayerRepository();
        });

        $this->app->singleton(GuildBalancerService::class, function ($app) {
            return new GuildBalancerService(
                $app->make(GuildRepository::class),
                $app->make(PlayerRepository::class),
                new ClassBalanceStrategy($app->make(PlayerRepository::class)),
                new XPBalanceStrategy()
            );
        });
    }

    public function boot()
    {
        // Outras lógicas de boot, caso necessário
    }
}
