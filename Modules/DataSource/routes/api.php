<?php

use Illuminate\Support\Facades\Route;
use Modules\DataSource\Http\Controllers\DataSourceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('datasources', DataSourceController::class)->names('datasource');
});
