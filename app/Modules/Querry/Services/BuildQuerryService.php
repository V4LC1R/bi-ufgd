<?php

namespace App\Modules\Querry\Services;

use App\Modules\Connection\Contracts\IStructTable;
use App\Modules\Connection\Http\DTOs\TableDTO;
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

        // fact (agregações e filtros)
        $this->fact($pre_sql->fact, $tables['fact']);

        // dimensões
        $this->dimensions($pre_sql->dimensions, $tables['dimension']);

        // sub-dimensões
        $this->subDimensions($pre_sql->subDimensions, $tables['sub-dimension'],$tables['dimension']);

        // gerar SQL
        $sql = $this->query->toSql();

        return $dump ? dd($sql) : $sql;
    }

    /**
     * 
     * @param  array<DimensionDTO> $pre_sql
     * @param array<string, TableDTO> $struct
    */

    private function dimensions(array $pre_sql, array $struct): void
    {
        foreach ($pre_sql as $dim) {
            foreach ($dim->columns as $col) {
                $this->query->addSelect("{$dim->table}.{$col}");
            }

            $this->joinsFromStruct($dim->table);
            $this->wheresFromFilter($dim->filter, $dim->table);
            $this->ordersFromOrder($dim->order, $dim->table);
        }
    }

    /**
     * 
     * @param  array<DimensionDTO> $pre_sql
     * @param array<string, TableDTO> $struct
     * @param array<string, TableDTO> $dimensions
    */
    private function subDimensions(array $pre_sql, array $struct, $dimensions): void
    {
        foreach ($pre_sql as $subDim) {
            foreach ($subDim->columns as $col) {
                $this->query->addSelect("{$subDim->table}.{$col}");
            }

            $this->joinsFromStruct($subDim->table);
            $this->wheresFromFilter($subDim->filter, $subDim->table);
            $this->ordersFromOrder($subDim->order, $subDim->table);
        }
    }

    private function fact(FactDTO $pre_sql, TableDTO $struct ): void
    {

        $this->query = DB::table($struct->name);

        $operations = [
            "avg" => function(Builder $q,string $col, string $alias){
                return $q->sum($col);
            },
            "sum",
            "count",
            "min",
            "max"
        ];

        //pegar os campos+operacoes+filtros+ordernacao

        foreach($pre_sql->columns as $col_name=> $actions){

            $selct = [];

            


        }
        
    }

    private function joinsFromStruct(string $table): void
    {
        $relations = $this->struct_service->getRelations($table);

        foreach ($relations as $col => $ref) {
            [$refTable, $refCol] = explode('.', $ref);
            $this->query->join($refTable, "{$table}.{$col}", '=', "{$refTable}.{$refCol}");
        }
    }

    private function wheresFromFilter(?array $filter, string $table): void
    {
        if (!$filter) return;

        foreach ($filter as $col => $condition) {
            $this->query->where("{$table}.{$col}", $condition['op'], $condition['value']);
        }
    }

    private function ordersFromOrder(?array $order, string $table): void
    {
        if (!$order) return;

        foreach ($order as $col => $dir) {
            $this->query->orderBy("{$table}.{$col}", $dir);
        }
    }
}
