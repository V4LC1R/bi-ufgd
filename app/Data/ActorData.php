<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Modules\Auth\Enums\RoleEnum;
use Spatie\LaravelData\Data;

class ActorData extends Data
{
    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public ?array $roles,
        public mixed $original
    ) {
    }

    public function can(string $permission): bool
    {

        if ($this->isReportApiKey())
            return false;

        if ($this->original && method_exists($this->original, 'can')) {
            return $this->original->can($permission);
        }

        return false;
    }

    public function canAssignRole(RoleEnum $role): bool
    {
        $hierarchy = [
            'admin' => 3,
            'analyst' => 2,
            'viewer' => 1,
        ];

        $actorLevel = max(array_map(
            fn($r) => $hierarchy[$r] ?? 0,
            $this->roles ?? []
        ));

        $targetLevel = $hierarchy[$role->value] ?? 0;

        return $actorLevel >= $targetLevel;
    }

    public function hasCommonRole(Collection $targetRoles): bool
    {
        return collect($this->roles)
            ->intersect($targetRoles)
            ->isNotEmpty();
    }

    public function isUser(): bool
    {
        return $this->type === 'user';
    }

    public function isApiKey(): bool
    {
        return $this->type === 'api_key';
    }

    public function isReportApiKey(): bool
    {
        return $this->isApiKey() && $this->original->type == 'report';
    }
}