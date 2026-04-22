<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Auth\Data\UpdateUserData;
use Modules\Auth\Http\Requests\CreateUserRequest;
use Modules\Auth\Http\Requests\UpdateUserRequest;
use Modules\Auth\Services\UserService;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function __construct(
        private UserService $service
    ) {
    }

    public function store(CreateUserRequest $request)
    {
        $dto = $request->toDto();

        $user = $this->service->create($dto);

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, $id)
    {

        $dto = UpdateUserData::from(array_merge($request->validated(), ['id' => $id]));

        $user = $this->service->update($dto);

        return response()->json($user, Response::HTTP_OK);
    }
}
