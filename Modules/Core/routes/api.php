<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

Route::middleware(['jwt'])->group(function () {
    Route::apiResource('cores', CoreController::class)->names('core');
});
