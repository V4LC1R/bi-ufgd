<?php

use Illuminate\Support\Facades\Route;
use Modules\Execution\Http\Controllers\ExecutionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('executions', ExecutionController::class)->names('execution');
});
