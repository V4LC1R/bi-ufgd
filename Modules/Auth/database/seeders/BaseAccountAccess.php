<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;

class BaseAccountAccess extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'api')->first();
        $analystRole = Role::where('name', 'analyst')->where('guard_name', 'api')->first();
        $viewerRole = Role::where('name', 'viewer')->where('guard_name', 'api')->first();

        $admin = User::firstOrCreate(
            ['email' => 'vblind@admin.com'],
            [
                'name' => 'V-blind Super Admin',
                'password' => Hash::make('@dmin!23'),
            ]
        );

        $admin->syncRoles([$adminRole]);

        $analyst = User::firstOrCreate(
            ['email' => 'vblind@analyst.com'],
            [
                'name' => 'V-blind Analyst',
                'password' => Hash::make('@nalyst!23'),
            ]
        );

        $analyst->syncRoles([$analystRole]);

        $viewer = User::firstOrCreate(
            ['email' => 'vblind@viewer.com'],
            [
                'name' => 'V-blind Viewer',
                'password' => Hash::make('vi&wer!23'),
            ]
        );

        $viewer->syncRoles([$viewerRole]);
    }
}
