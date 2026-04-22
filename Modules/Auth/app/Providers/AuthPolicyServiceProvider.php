<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Auth\Models\Key;
use Modules\Auth\Models\User;
use Modules\Auth\Policies\KeyPolicy;
use Modules\Auth\Policies\UserPolicy;

class AuthPolicyServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Key::class => KeyPolicy::class
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Auth::resolveUsersUsing(fn() => actor());
    }
}