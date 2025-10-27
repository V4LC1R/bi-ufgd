<?php
namespace App\Modules\User\Routes;

use App\Modules\Connection\Http\Controllers\ConnectionController;
use App\Modules\Connection\Http\Controllers\DimensionDataController;
use Illuminate\Support\Facades\Route;

Route::post('/', [ConnectionController::class, 'store']);
Route::patch('/{id}', [ConnectionController::class, 'edit']);
Route::get('/', [ConnectionController::class, 'index']);
Route::get('/{connection_name}/struct', [ConnectionController::class, 'struct']);
Route::get('/{conn_id}/dimension', [DimensionDataController::class, 'search']);


