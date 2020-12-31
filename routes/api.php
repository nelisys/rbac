<?php

use Illuminate\Support\Facades\Route;

use Nelisys\Rbac\Http\Controllers\ApiLoginController;

Route::group(['middleware' => ['api']], function () {
    Route::post('/api/login', [ApiLoginController::class, 'store']);
    Route::post('/api/logout', [ApiLoginController::class, 'destroy']);
});
