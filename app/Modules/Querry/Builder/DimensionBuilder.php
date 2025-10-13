<?php

namespace App\Modules\Querry\Builder;

use App\Modules\Querry\Http\DTOs\DimensionDTO;
use App\Modules\Connection\Http\DTOs\TableDTO;
use Illuminate\Database\Query\Builder;

class DimensionBuilder extends BaseBuilder
{
    protected ?Builder $query = null;
    protected $stack_coluns = [];
    protected bool $stack_initialized = false;

    /**
     * Summary of __construct
     * @param TableDTO $struct
     */
    public function __construct(protected TableDTO $fact)
    {
    }

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
    public static function fill(...$args): Builder
    {
        return (new self($args[0]))
            ->setQuery($args[2])
            ->build($args[1]);

    }

    public function build(...$args): Builder
    {


        $cols = [];
        $grouping = [];
        foreach ($args[0] as $dim) {
            $aliases = $this->columnsWithAliases($dim->table, $dim->columns, $dim->alias);
            $cols = array_merge(
                $cols,
                $aliases['as']
            );

            $grouping = array_merge(
                $grouping,
                $aliases['grouping']
            );

            //filter
            $this->filter($dim->table, $dim->filter);
            $this->joinInFact($dim->table);
        }

        $grouping = array_unique($grouping);

        if (!empty($grouping))
            $this->query->groupBy($grouping);

        if (!empty($cols)) {
            $this->query->addSelect($cols);
        }


        return $this->query;
    }


    private function joinInFact(string $dimension_table): void
    {
        $fact_table = $this->fact->name ?? null;

        if (!$fact_table || !$this->query) {
            return;
        }

        if (!$this->stack_initialized) {
            $this->stack_initialized = true;
            $this->stack_coluns = $this->fact->columns;
        }

        // Formato esperado: "type:fk:table.column"
        $pattern = '/fk:' . preg_quote($dimension_table, '/') . '\.([a-zA-Z0-9_]+)/';

        foreach ($this->stack_coluns as $col_name => $props) {

            // Testa se a coluna da fact referencia essa dimensão
            $hasMatch = preg_match($pattern, $props, $matches);

            if (!$hasMatch) {
                continue; // não é FK para essa dimensão, passa pra próxima
            }

            // Captura a coluna da dimensão
            $dimColumn = $matches[1];

            $this->query->join(
                $dimension_table,
                "{$dimension_table}.{$dimColumn}",
                '=',
                "{$fact_table}.{$col_name}"
            );

            unset($this->stack_coluns[$col_name]);
            // encontrou a FK, encerra o loop
            break;
        }
    }

}