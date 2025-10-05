<?php

namespace App\Modules\Querry\Builder;

use App\Modules\Querry\Http\DTOs\FactDTO;
use App\Modules\Connection\Http\DTOs\TableDTO;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FactBuilder extends BaseBuilder
{
    protected ?Builder $query = null;

    public function __construct(protected TableDTO $struct) {}

    public function setQuery(Builder $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Summary of fill
     * @param TableDTO $table
     * @param FactDTO $fact
     */
    public static function fill(...$args): Builder
    {
        $table = $args[0];
        $fact = $args[1];

        return (new self($table))
            ->setQuery(DB::table($table->name))
            ->build($fact);
    }

    /**
     * Summary of build
     * @param FactDTO $fact
     * @return Builder|null
     */
    public function build(...$fact): Builder
    {
       try {

        $selects = [];

        foreach ($fact[0]->columns as $colName => $actions) {
            // SELECT
            foreach ($actions->aggregates as $agg) {
                $select = $this->aggregationSelect($agg, $colName,$actions->alias[$agg] ?? null);
                if ($select) $selects[] = $select;
                
                $filter = $actions->filter[$agg] ?? null;
                if ($filter) {
                    $this->aggregationFilter($filter['op'], $filter['value'], $agg, $colName);
                }
            }
            
            foreach($actions->linear as $linear_op){
                if (isset($actions->filter[$linear_op])) {
                    $this->linearFilter($linear_op, $actions->filter[$linear_op], $colName);
                }
            }

        }

        if (!empty($selects)) {
            $this->query->addSelect($selects);
        }

        if ($fact[0]->limit > 0) {
            $this->query->limit($fact[0]->limit);
        }

        return $this->query;
       } catch (\Throwable $th) {
        dd($th);
       }
    }

    private function aggregationSelect(string $selected, string $col, ?string $alias = null)
    {
        // Lista branca de funções agregadoras permitidas
        $allowed = ['avg', 'sum', 'count', 'min', 'max'];
        $func = strtolower($selected);

        if (!in_array($func, $allowed, true)) 
            return null; // agregação não suportada

        // Escapa nomes de coluna e alias de forma segura
        $grammar = DB::getQueryGrammar();
        $colSafe = $grammar->wrap($col);
        $tableSafe = $grammar->wrapTable($this->struct->name);
        $aliasSql = $alias ? ' as ' . $grammar->wrap($alias) : '';

        // Monta a expressão final de forma segura
        return DB::raw(strtoupper($func) . "($tableSafe.$colSafe)$aliasSql");
    }

    private function aggregationFilter(string $op, mixed $value, string $agg, string $col): void
    {
        try {
            $aggExpr = $this->aggregationSelect($agg, $col);
        if (!$aggExpr) {
            return;
        }

        if ($op === ':range') {
        
            if (!is_array($value) || count($value) !== 2) return;
                $this->query->havingBetween( $aggExpr, [$value[0], $value[1]]);
            return;
        }

        $allowedOps = ['=', '!=', '>', '>=', '<', '<='];
        if (!in_array($op, $allowedOps, true)) {
            throw new InvalidArgumentException("Operator '{$op}' not allowed in aggregationFilter.");
        }

        $this->query->having($aggExpr, $op, $value);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    private function linearFilter(string $op, $value, string $col)
    {
        $dictionary = [
            "gt"=>">",
            "lt"=>"<",
            "eq"=>"=",
            "df"=>"!=",
            "gt-eq"=>">=",
            "ls-eq"=>"<=" 
        ];

        if(!array_key_exists($op,$dictionary))
            return;
        
        $this->query->where($col,$dictionary[$op],$value);
    }

    
}
