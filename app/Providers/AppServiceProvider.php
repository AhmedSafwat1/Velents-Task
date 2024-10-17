<?php

namespace App\Providers;

use App\Services\AuthenticationService;
use App\Services\Contracts\AuthenticationServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Bind Service
        $this->app->bind(AuthenticationServiceInterface::class , AuthenticationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
