<?php
namespace App\Modules\Querry\Routes;

use App\Modules\Querry\Http\Controllers\QuerryController;
use Illuminate\Support\Facades\Route;

Route::post('/', [QuerryController::class, "store"]);
Route::get('/result/{hash}', [QuerryController::class, "result"]);
Route::patch('/{id}', [QuerryController::class, "edit"]);
