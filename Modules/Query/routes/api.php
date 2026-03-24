<?php

use Illuminate\Support\Facades\Route;
use Modules\Query\Http\Controllers\QueryController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('queries', QueryController::class)->names('query');
});
