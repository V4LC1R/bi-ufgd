<?php
namespace App\Modules\Querry\Routes;

use App\Modules\Querry\Http\Controllers\QuerryController;
use Illuminate\Support\Facades\Route;

Route::post('/', [QuerryController::class, "store"]);
Route::patch('/{id}', [QuerryController::class, "edit"]);
Route::delete('/{id}', [QuerryController::class, "delete"]);
Route::get('/retry/{id}', [QuerryController::class, "retry"]);
Route::get('/by-connection/{id}', [QuerryController::class, "byConnectionId"]);
Route::get('/by-hash/{hash}', [QuerryController::class, "showByHash"]);

Route::get('/result/{hash}', [QuerryController::class, "result"]);
Route::get('/build/{id}', [QuerryController::class, "build"]);
