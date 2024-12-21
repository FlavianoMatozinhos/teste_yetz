<?php

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();
    
        Passport::routes(); // Registra as rotas do Passport
    }
}
