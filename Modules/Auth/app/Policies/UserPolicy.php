<?php

namespace Modules\Auth\Policies;

use App\Data\ActorData;
use Modules\Auth\Enums\RoleEnum;
use Modules\Auth\Models\User;

class UserPolicy
{
    public function viewAny(ActorData $actor): bool
    {
        return $actor->can('user.view');
    }

    public function view(ActorData $actor, User $user): bool
    {
        return $actor->id === $user->id
            || $actor->can('user.view');
    }

    public function create(ActorData $actor, RoleEnum $role): bool
    {
        return $actor->can('user.create')
            && $actor->canAssignRole($role);

    }

    public function update(ActorData $actor, User $user, RoleEnum $role): bool
    {
        // pode atualizar a si mesmo (sem mudar role)
        if ($actor->isUser() && $actor->id === $user->id) {
            return true;
        }

        if (!$actor->can('user.update')) {
            return false;
        }

        if ($actor->hasCommonRole($user->getRoleNames())) {
            return false;
        }

        // só valida hierarquia se for trocar role
        if (!$user->getRoleNames()->contains($role->value)) {
            if (!$actor->canAssignRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function delete(ActorData $actor, User $user): bool
    {
        if ($actor->isUser() && $actor->id === $user->id) {
            return false;
        }

        if (!$actor->can('user.delete')) {
            return false;
        }

        if ($actor->isUser() && $actor->hasCommonRole($user->getRoleNames())) {
            return false;
        }

        return true;
    }
}