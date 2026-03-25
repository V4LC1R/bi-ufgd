<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Data\CreateUserData;
use Modules\Auth\Http\Requests\UserRequest;
use Modules\Auth\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function __construct(
        private UserService $service
    ) {
    }

    public function store(UserRequest $request)
    {
        $dto = CreateUserData::from($request->validated());

        $user = $this->service->create($dto);

        return response()->json($user, Response::HTTP_CREATED);
    }
}
