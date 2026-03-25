<?php

namespace Modules\Auth\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Data\CreateUserData;
use Modules\Auth\Exceptions\UserAlreadyExistsException;
use Modules\Auth\Models\User;

class UserService
{
    public function create(CreateUserData $dto)
    {
        try {
            return DB::transaction(function () use ($dto) {

                $user = User::create($dto->toArray());

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
        return str_contains($e->getMessage(), 'unique');
    }
}