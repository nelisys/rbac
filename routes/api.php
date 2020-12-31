<?php

use Illuminate\Support\Facades\Route;

use Nelisys\Rbac\Http\Controllers\TokenController;

Route::group(['middleware' => ['api']], function () {
    Route::post('/api/login', [TokenController::class, 'store']);
    Route::post('/api/logout', [TokenController::class, 'destroy']);
});
