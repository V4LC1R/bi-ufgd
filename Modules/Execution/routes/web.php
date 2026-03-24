<?php

use Illuminate\Support\Facades\Route;
use Modules\Execution\Http\Controllers\ExecutionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('executions', ExecutionController::class)->names('execution');
});
