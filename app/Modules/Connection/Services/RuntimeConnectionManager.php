<?php

namespace App\Modules\Connection\Services;

use App\Modules\Connection\Builder\DynamicConnectionBuilder;
use App\Modules\Connection\Contracts\DynamicConnectionManager;
use App\Modules\Connection\Models\Connection;
use Illuminate\Support\Facades\DB;

class RuntimeConnectionManager implements DynamicConnectionManager
{
    /**
     * {@inheritdoc}
     */
    public function setup(Connection $connection): string
    {
        $connectionName = "dynamic_connection_{$connection->id}";

        // 1. Delega a construção da configuração para o Builder
        $config = DynamicConnectionBuilder::fromModel($connection);

        // 2. Injeta a configuração no sistema do Laravel
        config()->set("database.connections.{$connectionName}", $config);

        // 3. Força o Laravel a descartar qualquer conexão antiga em cache com este nome
        DB::purge($connectionName);

        // 4. Retorna o nome da conexão pronta para ser usada
        return $connectionName;
    }
}