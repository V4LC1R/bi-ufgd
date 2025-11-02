<?php

namespace App\Modules\Connection\Contracts;

use App\Modules\Querry\Http\DTOs\PreSqlDTO;

/**
 * Interface para serviços que gerenciam a configuração e ativação
 * de conexões de banco de dados em tempo de execução.
 */
interface FieldRelationResult
{

    public function setup(PreSqlDTO $pre_sql): array;
}