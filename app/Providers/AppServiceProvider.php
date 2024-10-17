<?php

namespace App\Providers;

use App\Services\OrderService;
use App\Services\AuthenticationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\AuthenticationServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Bind Service
        $this->app->bind(AuthenticationServiceInterface::class , AuthenticationService::class);
        $this->app->bind(OrderServiceInterface::class , OrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
