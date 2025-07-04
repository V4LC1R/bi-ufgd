<?php

namespace App\Modules\Auth\Http\DTOs;

class SignUpDTO
{
    public function __construct(
        public readonly string $password,
        public readonly string $document,
        public readonly int $entity_id,
        public readonly string $email,
        public readonly string $name,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            password: $data['password'],
            document: $data['document'],
            entity_id: (int) $data['entity_id'],
            email: $data['email'],
            name: $data['name'],
        );
    }
}
