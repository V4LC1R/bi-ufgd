<?php
namespace App\Modules\Reports\Routes;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
