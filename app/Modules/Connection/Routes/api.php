<?php
namespace App\Modules\User\Routes;

use App\Modules\Connection\Http\Controllers\ConnectionController;
use App\Modules\Connection\Http\Controllers\DimensionDataController;
use Illuminate\Support\Facades\Route;

Route::post('/', [ConnectionController::class, 'store']);
Route::patch('/{id}', [ConnectionController::class, 'edit']);
Route::get('/{table_id}/dimension', [DimensionDataController::class, 'search']);
Route::get('/exec/{query_id}', [ConnectionController::class, 'exec']);


