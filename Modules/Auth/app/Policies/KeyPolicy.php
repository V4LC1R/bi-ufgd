<?php

namespace Modules\Auth\Policies;

use App\Data\ActorData;
use Modules\Auth\Enums\RoleEnum;
use Modules\Auth\Models\Key;

class KeyPolicy
{
    public function viewAny(ActorData $actor): bool
    {
        return $actor->can('apikey.view');
    }

    public function view(ActorData $actor, Key $key): bool
    {
        return ($actor->isApiKey() && $actor->id === $key->id)
            || $actor->can('apikey.view');
    }

    public function create(ActorData $actor, RoleEnum $role): bool
    {
        return $actor->can('apikey.create')
            && $actor->canAssignRole($role);
        ;
    }

    public function update(ActorData $actor, Key $key, RoleEnum $role): bool
    {
        if ($actor->isApiKey() && $actor->id === $key->id) {
            return true;
        }

        if (!$actor->can('apikey.update')) {
            return false;
        }

        if ($actor->hasCommonRole($key->getRoleNames())) {
            return false;
        }

        if (!$key->getRoleNames()->contains($role->value)) {
            if (!$actor->canAssignRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function delete(ActorData $actor, Key $key): bool
    {
        // não pode deletar a si mesmo (opcional, mas recomendado)
        if ($actor->isApiKey() && $actor->id === $key->id) {
            return false;
        }

        if (!$actor->can('apikey.delete')) {
            return false;
        }

        if ($actor->isApiKey() && $actor->hasCommonRole($key->getRoleNames())) {
            return false;
        }

        return true;

    }

    public function assignRole(ActorData $actor): bool
    {
        return $actor->can('apikey.assign-role');
    }
}