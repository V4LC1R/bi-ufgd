<?php

use Illuminate\Support\Facades\Route;
use Modules\Schema\Http\Controllers\SchemaController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('schemas', SchemaController::class)->names('schema');
});
