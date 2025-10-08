<?php
namespace App\Modules\User\Routes;

use App\Modules\Connection\Http\Controllers\ConnectionController;
use Illuminate\Support\Facades\Route;

Route::post('/', [ConnectionController::class,'store']);
Route::get('/{query_id}/{connection_id}', [ConnectionController::class,'exec']);
