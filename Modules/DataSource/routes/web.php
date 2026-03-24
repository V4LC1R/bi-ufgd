<?php

use Illuminate\Support\Facades\Route;
use Modules\DataSource\Http\Controllers\DataSourceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('datasources', DataSourceController::class)->names('datasource');
});
