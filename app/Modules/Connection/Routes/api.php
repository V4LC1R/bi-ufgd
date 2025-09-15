<?php
namespace App\Modules\User\Routes;

use App\Modules\Connection\Http\Controllers\ConnectionController;
use Illuminate\Support\Facades\Route;

Route::post('/', [ConnectionController::class,'store']);
