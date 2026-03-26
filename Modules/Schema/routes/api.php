<?php

use Illuminate\Support\Facades\Route;
use Modules\Schema\Http\Controllers\SchemaController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('schemas', SchemaController::class)->names('schema');
});
