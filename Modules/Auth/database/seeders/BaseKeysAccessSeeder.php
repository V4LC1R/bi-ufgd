<?php

namespace Modules\Auth\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\Key;
use Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;

class BaseKeysAccessSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'api')->first();
        $analystRole = Role::where('name', 'analyst')->where('guard_name', 'api')->first();

        $admin = Key::firstOrCreate(
            ['name' => 'admin-key'],
            [
                'hash' => Hash::make(Carbon::now()),
            ]
        );

        $admin->syncRoles([$adminRole]);

        $analyst = User::firstOrCreate(
            ['name' => 'analyst-key'],
            [
                'hash' => Hash::make(Carbon::now()),
            ]
        );

        $analyst->syncRoles([$analystRole]);

        User::firstOrCreate(
            ['name' => 'view-key'],
            [
                'hash' => Hash::make(Carbon::now()),
            ]
        );
    }
}
