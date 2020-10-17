<?php

namespace Nelisys\Rbac;

use Hash;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LockoutResponse as LockoutResponseContract;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Responses\LockoutResponse;
use Laravel\Fortify\Http\Responses\LoginResponse;
use Laravel\Fortify\Http\Responses\LogoutResponse;

use Nelisys\Rbac\Models\User;

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
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/fortify.php' => config_path('fortify.php'),
                __DIR__ . '/../config/nelisys/rbac.php' => config_path('nelisys/rbac.php'),
            ]);

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        Fortify::authenticateUsing(function (Request $request) {
            $username = config('fortify.username');

            $user = User::where($username, $request->{$username})
                ->where('is_active', true)
                ->first();

            if ($user &&
                Hash::check($request->password, $user->password)) {
                return $user;
            }
        });
    }
}
