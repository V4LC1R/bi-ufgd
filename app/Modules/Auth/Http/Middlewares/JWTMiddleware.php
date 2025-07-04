<?php
namespace App\Units\Core\Http\Middleware;

use Closure;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class JWTMiddleware extends BaseMiddleware{

    public function handle($request,Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();

        } catch (\Exception $th) {
            if($th instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    "message"=>"Token Invalid"
                ],401);
            }else if($th instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    "message"=>"Token Expired"
                ],401);
            }else {
                return response()->json([
                    "message"=>$th->getMessage()
                ],401);
            }

        }

        return $next($request);
       
    }

}