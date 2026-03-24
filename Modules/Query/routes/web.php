<?php

use Illuminate\Support\Facades\Route;
use Modules\Query\Http\Controllers\QueryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('queries', QueryController::class)->names('query');
});
