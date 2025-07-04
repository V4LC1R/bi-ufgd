<?php
namespace App\Modules\Auth\Routes;

use App\Modules\Auth\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/',[AuthController::class,'signIn']);
Route::post('/register',[AuthController::class,'signUp']);
Route::put('/change-password',[AuthController::class,'changePassword']);
Route::post('/request-change',[AuthController::class,'requestChangePassword']);
