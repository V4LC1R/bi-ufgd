<?php

use Illuminate\Support\Facades\Route;
use Modules\Execution\Http\Controllers\ExecutionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('executions', ExecutionController::class)->names('execution');
});
