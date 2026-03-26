<?php

namespace Modules\Auth\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Data\CreateUserData;
use Modules\Auth\Exceptions\UserAlreadyExistsException;
use Modules\Auth\Models\User;

class UserService
{
    public function create(CreateUserData $dto)
    {
        try {
            return DB::transaction(function () use ($dto) {

                $data = $dto->toArray();
                $data['password'] = Hash::make($dto->password);

                $user = User::create($data);

                return $user;
            });
        } catch (QueryException $e) {

            if ($this->isUniqueConstraint($e)) {
                throw new UserAlreadyExistsException();
            }

            return $e;
        }
    }

    private function isUniqueConstraint(QueryException $e): bool
    {
        return in_array($e->getCode(), ['23505', '1062']);
    }
}