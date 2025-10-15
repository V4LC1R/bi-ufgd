<?php
namespace App\Modules\User\Routes;

use App\Modules\Connection\Http\Controllers\ConnectionController;
use App\Modules\Connection\Http\Controllers\DimensionDataController;
use Illuminate\Support\Facades\Route;

Route::post('/', [ConnectionController::class, 'store']);
Route::get('/dimension', [DimensionDataController::class, 'search']);
Route::get('/{query_id}', [ConnectionController::class, 'exec']);


