<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Http\Request\ChangePasswordRequest;
use App\Modules\Auth\Http\Request\LoginRequest;
use App\Modules\Auth\Http\Request\RequestChangePasswordRequest;
use App\Modules\Auth\Http\Request\SignUpRequest;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function signIn(LoginRequest $request)
    {
        $token = Auth::setTTL(600)->attempt($request->only('email', 'password'));

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function singUp(SignUpRequest $request)
    {
        // $user = $service->create($request);
    
        // $token = Auth::setTTL(600)->login($user);
        // return response()->json([
        //     'user' => $user,
        //     'token' => $token
        // ]);
    }


    public function changePassword(ChangePasswordRequest $request)
    {

    }

    public function requestChangePassword(RequestChangePasswordRequest $request)
    {
        
    }
}
