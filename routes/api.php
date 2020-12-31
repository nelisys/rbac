<?php

use Illuminate\Support\Facades\Route;

use Nelisys\Rbac\Http\Controllers\LoginController;

Route::group(['prefix' => 'api', 'middleware' => ['api']], function () {
    Route::post('login', [LoginController::class, 'store']);
    Route::post('logout', [LoginController::class, 'destroy']);
});
