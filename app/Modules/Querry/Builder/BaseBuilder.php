<?php
namespace App\Modules\Querry\Builder;

use App\Modules\Connection\Http\DTOs\TableDTO;
use App\Modules\Querry\Contract\TransformPreSql;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class BaseBuilder implements TransformPreSql
{
    protected ?Builder $query = null;


    public function build(...$args): Builder
    {
        return $this->query;
    }

    public static function fill(...$args): Builder
    {
        return (new self())->build($args);
    }

    protected function joinInTable(string $start_table, TableDTO $goal_table, ?string $reg_ex = null)
    {
        $pattern = $reg_ex ?? '/fk:' . preg_quote($start_table, '/') . '\.([a-zA-Z0-9_]+)/';

        foreach ($goal_table->columns as $col_name => $props) {
            $hasMatch = preg_match($pattern, $props, $matches);

            if (!$hasMatch) {
                continue; // não é FK para essa dimensão, passa pra próxima
            }
            $start_table_column = $matches[1];

            $this->query->join(
                $start_table,
                "{$goal_table->name}.{$col_name}",
                '=',
                "{$start_table}.{$start_table_column}"
            );
            break;
        }
    }

    protected function columnsWithAliases(string $table, array $coluns, array $alias): array
    {
        $as = [];
        $to_grouping = [];

        foreach ($coluns as $col) {

            $col_wrap = $table . '.' . $col;

            if (!array_key_exists($col, $alias)) {
                $as[] = $col_wrap;
                $to_grouping[] = $table . '.' . $col;
                continue;
            }

            $col_as = $col_wrap . " as " . $alias[$col];
            $as[] = $col_as;
            $to_grouping[] = $alias[$col];
        }

        return [
            "as" => $as,
            "grouping" => $to_grouping
        ];
    }

    /**
     * Summary of filter
     * @param string $table
     * @param array<string,array> $filters
     * @return void
     */
    protected function filter(string $table, array $filters)
    {
        $grammar = $this->query->getGrammar();
        ;
        $table_wrap = $grammar->wrapTable($table);

        foreach ($filters as $col => $filter) {
            $col_wrap = $table_wrap . '.' . $grammar->wrap($col);
            $allowedOps = ['=', '!=', '>', '>=', '<', '<=', 'like', ':range']; // :range mantido

            if (!in_array($filter['op'], $allowedOps, true)) {
                continue;
            }

            if ($filter['op'] === ':range') {

                if (is_array($filter['value']) && count($filter['value']) === 2) {
                    // Usamos BETWEEN ? AND ? e passamos o array de valores
                    $this->query->whereRaw($col_wrap . ' BETWEEN ? AND ?', $filter['value']);
                }

            } else {

                $bindings = [$filter['value']];
                $this->query->whereRaw($col_wrap . ' ' . $filter['op'] . ' ?', $bindings);
            }
        }

    }
}