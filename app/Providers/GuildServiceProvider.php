<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GuildBalancerService;

class GuildServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(GuildBalancerService::class, function ($app) {
            return new GuildBalancerService();
        });
    }

    public function boot()
    {
        //
    }
}
