<?php

use Illuminate\Support\Facades\Route;
use Modules\DataSource\Http\Controllers\DataSourceController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('datasources', DataSourceController::class)->names('datasource');
});
