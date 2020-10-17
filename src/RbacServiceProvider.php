<?php

namespace Nelisys\Rbac;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LockoutResponse as LockoutResponseContract;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Http\Responses\LockoutResponse;
use Laravel\Fortify\Http\Responses\LoginResponse;
use Laravel\Fortify\Http\Responses\LogoutResponse;

class RbacServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(LockoutResponseContract::class, LockoutResponse::class);
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);

        $this->app->bind(StatefulGuard::class, function () {
            return Auth::guard(config('fortify.guard', 'web'));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/fortify.php' => config_path('fortify.php'),
            __DIR__ . '/../config/nelisys/rbac.php' => config_path('nelisys/rbac.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }
}
