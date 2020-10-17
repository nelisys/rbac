# Nelisys RBAC for Laravel

## Introduction

Role-Based Access Control for Laravel.

## Installation

Run composer to install the package.

```
composer require nelisys/rbac
```

Publish the configuration files.

```
php artisan vendor:publish --provider="Nelisys\Rbac\RbacServiceProvider"
```

Change `users.model` to `Nelisys\Rbac\Models\User`

```php
// config/auth.php
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => Nelisys\Rbac\Models\User::class,
        ],
```

Add Sanctum middleware in `api`.

```php
// app/Http/Kernel.php
    protected $middlewareGroups = [
        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
```

Run migrate.

```
php artisan migrate
```

## License

Nelisys RBAC is open-sourced software licensed under the [MIT license](LICENSE.md).
