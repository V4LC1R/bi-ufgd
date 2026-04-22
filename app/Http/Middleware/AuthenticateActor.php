<?php

namespace App\Http\Middleware;

use App\Data\ActorData;
use Closure;
use Illuminate\Http\Request;
use Modules\Auth\Models\Key;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthenticateActor
{
    public function handle(Request $request, Closure $next): Response
    {
        [$actor, $authType] = $this->jwtAuth();

        if (!$actor) {
            [$actor, $authType] = $this->keyAuth($request);
        }

        if (!$actor || !$authType) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $this->createContext($request, $actor, $authType);

        return $next($request);
    }

    private function jwtAuth(): array
    {
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                return [$user, 'user'];
            }
        } catch (\Throwable $e) {

        }

        return [null, null];
    }

    private function keyAuth(Request $request): array
    {
        $apiKey = $request->header('X-API-KEY');

        if ($apiKey) {
            $key = Key::where('is_active', true)->get()
                ->first(fn($k) => $k->checkKey($apiKey));

            if ($key) {
                return [$key, 'api_key'];
            }
        }

        return [null, null];
    }


    private function createContext(Request $request, $actor, string $authType): void
    {
        $data = ActorData::from([
            'id' => $actor->id,
            'type' => $authType,
            'name' => $actor->name ?? 'API Key',
            'roles' => method_exists($actor, 'getRoleNames')
                ? $actor->getRoleNames()->toArray()
                : [],
            'original' => $actor,
        ]);

        $request->attributes->set('actor', $data);

        app()->instance('actor', $data);

        if ($authType === 'user') {
            auth()->setUser($actor);
        }
    }
}