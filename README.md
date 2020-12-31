# Nelisys RBAC for Laravel

<p align="center">
    <a href="https://packagist.org/packages/nelisys/rbac"><img src="https://poser.pugx.org/nelisys/rbac/d/total.svg" alt="Total Downloads"></a>
</p>

## Introduction

Role-Based Access Control for Laravel.

Default the package will use `username` column to authenticate. If you want to use `email` instead, just change `username` to `email` in `config/nelisys/rbac.php`.

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

Add sanctum config in `.env` file

```
SANCTUM_STATEFUL_DOMAINS=example.com
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

## Create Test User

Run `php artisan tinker` to create test user.

```
Nelisys\Rbac\Models\User::create([
    'username' => 'alice',
    'password' => bcrypt('secret'),
]);
```

## Test API by curl

### API Login

Call `/api/login` to get the token.

```
$ curl \
    -H 'X-Requested-With: XMLHttpRequest' \
    -d 'username=alice&password=secret' \
    http://example.test/api/login
```

### API Authorization

Specify header `Authorization: Bearer` with the token return.

Note: Replace `$token` with the token return.

```
$ curl \
    -H 'X-Requested-With: XMLHttpRequest' \
    -H 'Authorization: Bearer $token' \
    http://example.test/api/user

{
  "username" : "alice",
  "id" : 1,
  ...
}
```

### API Logout

Specify header `Authorization: Bearer` with the token return.

Note: Replace `$token` with the token return.

```
$ curl \
    -X POST \
    -H 'X-Requested-With: XMLHttpRequest' \
    -H 'Authorization: Bearer $token' \
    http://example.test/api/logout
```

## License

Nelisys RBAC is open-sourced software licensed under the [MIT license](LICENSE.md).
