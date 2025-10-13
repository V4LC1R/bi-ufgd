<?php

namespace App\Modules\Connection\Contracts;

use App\Modules\Connection\Models\Connection;

/**
 * Interface para serviços que gerenciam a configuração e ativação
 * de conexões de banco de dados em tempo de execução.
 */
interface DynamicConnectionManager
{
    /**
     * Configura uma conexão de banco de dados em tempo de execução a partir de um modelo Eloquent,
     * a registra no gerenciador do Laravel e a torna pronta para uso.
     *
     * @param Connection $connection O modelo Eloquent contendo os detalhes da conexão.
     * @return string O nome único e temporário da conexão que foi ativada (ex: 'dynamic_connection_5').
     */
    public function setup(Connection $connection): string;
}