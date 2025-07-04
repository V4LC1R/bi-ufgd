<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Http\Request\ChangePasswordRequest;
use App\Modules\Auth\Http\Request\LoginRequest;
use App\Modules\Auth\Http\Request\RequestChangePasswordRequest;
use App\Modules\Auth\Http\Request\SignUpRequest;
use App\Modules\Auth\Services\AuthService;
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
        unset($user["password"]);

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function signUp(SignUpRequest $request,AuthService $userService)
    {
        $user = $userService->createUser($request->toDTO());
    
        $token = Auth::setTTL(600)->login($user);

        unset($user["password"]);
        
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }


    public function changePassword(ChangePasswordRequest $request)
    {

    }

    public function requestChangePassword(RequestChangePasswordRequest $request)
    {
        
    }
}
