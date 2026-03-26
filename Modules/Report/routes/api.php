<?php

use Illuminate\Support\Facades\Route;
use Modules\Report\Http\Controllers\ReportController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('reports', ReportController::class)->names('report');
});
