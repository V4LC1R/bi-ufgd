<?php

namespace App\Modules\Querry\Builder;

use App\Modules\Querry\Http\DTOs\DimensionDTO;
use App\Modules\Connection\Http\DTOs\TableDTO;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DimensionBuilder
{
    protected ?Builder $query = null;
    protected $stack_coluns = [];
    protected bool $stack_initialized = false;
    
    /**
     * Summary of __construct
     * @param TableDTO $struct
     */
    public function __construct(protected TableDTO $fact) {}

    public function setQuery(Builder $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Summary of fill
     * @param TableDTO $fact
     * @param array<string,DimensionDTO> $dimensions
     * @param Builder $query
     */
    public static function fill(TableDTO $fact,array $dimensions,Builder $query)
    {
        return (new self($fact))
            ->setQuery($query)
            ->build($dimensions);
    }

    /**
     * Summary of build
     * @param array<string,DimensionDTO> $dimensions
     * @throws \Exception
     * @return void
     */
    public function build(array $dimensions)
    {
        if(!$this->query)
            throw new \Exception("Querry acc not found!");

        $cols = [];
        $grouping = [];

        foreach ($dimensions as $table=> $dim) {
            $aliases = $this->columnsWithAliases($table,$dim->columns,$dim->alias);

            $cols = array_merge(
                $cols,
                $aliases['as']
            );

            $grouping = array_merge(
                $grouping,
                $aliases['grouping']
            );

            //filter
            $this->filter($table,$dim->filter);
            $this->joinInFact($table);
        }

        $grouping = array_unique($grouping);

        if(!empty($grouping))
            $this->query->groupBy($grouping);
        

        return $this->query;
    }

    private function columnsWithAliases(string $table,array $coluns, array $alias) :array
    {
        $as = [];
        $to_grouping = [];
        $grammar = DB::getQueryGrammar();
        $table_wrap = $grammar->wrapTable($table);

        foreach($coluns as $col){

            $col_wrap = $table_wrap . '.' . $grammar->wrap($col);
            
            if(!array_key_exists($col, $alias)) {
                $as[]= $col_wrap;
                $to_grouping[]=$col_wrap;
                continue;
            }

            $col_as_wrap = $grammar->wrap($alias[$col]);
            $col_as = $col_wrap." as ".$col_as_wrap; 
            $as[]= $col_as;
            $to_grouping[] = $col_as_wrap;
        }

        return [
            "as"=>$as,
            "grouping"=>$to_grouping
        ];
    }

    /**
     * Summary of filter
     * @param string $table
     * @param array<string,array> $filters
     * @return void
     */
    private function filter(string $table, array $filters)
    {
        $grammar = DB::getQueryGrammar();
        $table_wrap = $grammar->wrapTable($table);

        foreach ($filters as $col => $filter) {
            $col_wrap = $table_wrap . '.' . $grammar->wrap($col);
            $allowedOps = ['=', '!=', '>', '>=', '<', '<=', 'like'];
            if(!in_array($filter['op'], $allowedOps, true)) continue;
            $this->query->where($col_wrap,$filter['op'],$filter['value']);
        }

    }

    private function joinInFact(string $dimension_table): void
    {
        $fact_table = $this->fact->name ?? null;

        if (! $fact_table || ! $this->query) {
            return;
        }

        if(!$this->stack_initialized){
            $this->stack_initialized = true;
            $this->stack_coluns = $this->fact->columns;
        }
            
        $grammar          = DB::getQueryGrammar();
        $fact_table_wrap  = $grammar->wrapTable($fact_table);
        $dim_table_wrap   = $grammar->wrapTable($dimension_table);

        foreach ($this->stack_coluns as $col_name => $props) {
            // Formato esperado: "type:fk:table.column"
            if (!preg_match('/fk:' . preg_quote($dimension_table, '/') . '\.([a-zA-Z0-9_]+)/', $props, $matches)) {
                continue;
            }

            $dim_col_wrap  = $grammar->wrap($matches[1]);
            $fact_col_wrap = $grammar->wrap($col_name);

            $this->query->join(
                $dimension_table,
                "{$dim_table_wrap}.{$dim_col_wrap}",
                '=',
                "{$fact_table_wrap}.{$fact_col_wrap}"
            );

            unset($this->stack_coluns[$col_name]);
            // encontrou a FK, encerra o loop
            break;
        }
    }

}