<?php
namespace App\Modules\Querry\Routes;

use App\Modules\Querry\Http\Controllers\QuerryController;
use Illuminate\Support\Facades\Route;

Route::post('/', [QuerryController::class, "store"]);
Route::get('/by-connection/{id}', [QuerryController::class, "byConnectionId"]);
Route::get('/result/{hash}', [QuerryController::class, "result"]);
Route::patch('/{id}', [QuerryController::class, "edit"]);

Route::get('/build/{id}', [QuerryController::class, "build"]);
