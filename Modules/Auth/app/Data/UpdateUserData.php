<?php

namespace Modules\Auth\Data;

use Modules\Auth\Enums\RoleEnum;
use Spatie\LaravelData\Data;

class UpdateUserData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly RoleEnum $role,
        public readonly int $id
    ) {
    }
}