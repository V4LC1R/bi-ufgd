<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::middleware(['jwt'])->group(function () {
    Route::apiResource('auths', AuthController::class)->names('auth');
});
