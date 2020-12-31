<?php

namespace Nelisys\Rbac;

use Hash;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

use Nelisys\Rbac\Models\User;

class RbacServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/nelisys/rbac.php' => config_path('nelisys/rbac.php'),
            ]);

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $username = config('nelisys.rbac.username');
    }
}
