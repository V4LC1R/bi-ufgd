<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // DataSource
            'datasource.create',
            'datasource.view',
            'datasource.update',
            'datasource.delete',
            'datasource.healthcheck',
            'datasource.introspect',    // lê o schema real do banco

            // Schema
            'schema.view',
            'schema.update',            // editar aliases, relacionamentos, etc
            'schema.validate',

            // Query
            'query.create',
            'query.view',
            'query.update',
            'query.delete',
            'query.build',              // gera o AST/SQL sem executar
            'query.execute',            // dispara execução real

            // Report
            'report.create',
            'report.view',
            'report.update',
            'report.delete',
            'report.execute',
            'report.export',

            // API Key
            'apikey.create',
            'apikey.view',
            'apikey.delete',
            'apikey.assign-reports',    // vincula reports a uma report-scoped key

            // User
            'user.create',
            'user.view',
            'user.update',
            'user.delete',
            'user.assign-role',
        ];

        // Criar permissions com guard correto
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }

        // Roles
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'api'
        ]);

        $analyst = Role::firstOrCreate([
            'name' => 'analyst',
            'guard_name' => 'api'
        ]);

        $viewer = Role::firstOrCreate([
            'name' => 'viewer',
            'guard_name' => 'api'
        ]);

        // ADMIN → tudo
        $admin->givePermissionTo(Permission::all());

        // ANALYST
        $analyst->givePermissionTo([
            'datasource.view',
            'datasource.introspect',

            'schema.view',
            'schema.update',
            'schema.validate',

            'query.create',
            'query.view',
            'query.update',
            'query.build',
            'query.execute',

            'report.create',
            'report.view',
            'report.update',
            'report.execute',

            'apikey.create',
            'apikey.view',
        ]);

        // VIEWER
        $viewer->givePermissionTo([
            'query.view',
            'query.execute',

            'report.view',
            'report.execute',
        ]);
    }
}
