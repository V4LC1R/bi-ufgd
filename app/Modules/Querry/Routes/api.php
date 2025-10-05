<?php
namespace App\Modules\Reports\Routes;

use App\Modules\Querry\Http\Controllers\QuerryController;
use Illuminate\Support\Facades\Route;

Route::post('/', [QuerryController::class,"store"]);
Route::get('/{id}', [QuerryController::class,"teste"]);
