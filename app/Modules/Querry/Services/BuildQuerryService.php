<?php

namespace App\Modules\Querry\Services;

use App\Modules\Connection\Contracts\IStructTable;
use App\Modules\Connection\Http\DTOs\TableDTO;
use App\Modules\Querry\Builder\DimensionBuilder;
use App\Modules\Querry\Builder\FactBuilder;
use App\Modules\Querry\Http\DTOs\FactDTO;
use App\Modules\Querry\Http\DTOs\DimensionDTO;
use App\Modules\Querry\Http\DTOs\PreSqlDTO;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class BuildQuerryService
{
    protected ?Builder $query = null;

    public function __construct(
        protected IStructTable $struct_service
    ) {}

    public function makeQuerry(PreSqlDTO $pre_sql, bool $dump = false): string
    {
        $this->struct_service->setConnectionName($pre_sql->connectionName);

        $tables = $this->struct_service->getStructConnection();

        $fact_table = array_values($tables['fact'])[0];

        // fact (agregações e filtros)
        FactBuilder::fill( $fact_table ,$pre_sql->fact,$this->query);

        // dimensões
        DimensionBuilder::fill($fact_table,$pre_sql->dimensions,$this->query);
      
        // sub-dimensões

        // gerar SQL
        $sql = $this->query->toSql();

        return $dump ? dd($sql) : $sql;
    }

}
