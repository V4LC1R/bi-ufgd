<?php

namespace App\Modules\Querry\Builder;

use App\Modules\Querry\Http\DTOs\FactDTO;
use App\Modules\Connection\Http\DTOs\TableDTO;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class FactBuilder
{
    protected Builder $query;

    public function __construct(protected TableDTO $struct) {}

    public function build(FactDTO $fact): Builder
    {
        $this->query = DB::table($this->struct->name);

        $selects = [];

        $filters = [];

        foreach ($fact->columns as $colName => $actions) {
            // SELECT
            foreach ($actions->aggregates as $agg) {
                $select = $this->aggregationSelect($agg, $colName, $actions->as[$agg] ?? null);
                if ($select) $selects[] = $select;
                
                $action = $actions->filter[$agg];
                $this->aggregationFilter($action["op"],$action['value'],$agg,$colName);
               
            }

        }

        if (!empty($selects)) {
            $this->query->select($selects);
        }

        if ($fact->limit > 0) {
            $this->query->limit($fact->limit);
        }

        return $this->query;
    }

    private function aggregationSelect(string $selected, string $col, ?string $alias = null)
    {
        $aggregations = [
            "avg"   => fn (string $col, string $alias) => DB::raw("AVG($col) $alias"),
            "sum"   => fn (string $col, string $alias) => DB::raw("SUM($col) $alias"),
            "count" => fn (string $col, string $alias) => DB::raw("COUNT($col) $alias"),
            "min"   => fn (string $col, string $alias) => DB::raw("MIN($col) $alias"),
            "max"   => fn (string $col, string $alias) => DB::raw("MAX($col) $alias"),
        ];

        if (!array_key_exists($selected, $aggregations)) {
            return null;
        }

        $aliasDefine = !!$alias ? "as {$alias}" : "";
        return $aggregations[$selected]($col, $aliasDefine);
    }

    private function aggregationFilter(string $op, $value, string $agg, string $col)
    {

        $agg_querry = $this->aggregationSelect($agg,$col);

        if ($op === ':range') {
            $this->query->havingBetween($agg_querry, $value);
            return;
        }
        
        $this->query->having($agg_querry, $op, $value);

    }

    private function linearFilter(string $op, $value, string $col)
    {

    }
}
