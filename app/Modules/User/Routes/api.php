<?php
namespace App\Modules\User\Routes;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(["message"=>"Teu cu q deu certo"]);
});

Route::get('so-corno',function () {
    return response()->json(["message"=>"Sou foda!"]);
});
