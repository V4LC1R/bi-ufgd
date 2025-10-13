<?php

namespace App\Modules\Querry\Builder;

use App\Modules\Querry\Http\DTOs\SubDimensionDTO;
use App\Modules\Connection\Http\DTOs\TableDTO;
use Illuminate\Database\Query\Builder;

class SubDimensionBuilder extends BaseBuilder
{
    protected ?Builder $query = null;

    /**
     * Summary of __construct
     * @param array<string,TableDTO> $dimensions
     */
    public function __construct(protected array $dimensions)
    {
    }

    public function setQuery(Builder $query): self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Summary of fill
     * @param array<string,TableDTO> $dimensions
     * @param array<string,SubDimensionDTO> $sub_dimensions
     * @param Builder $query
     */
    public static function fill(...$args): Builder
    {
        return (new self($args[0]))
            ->setQuery($args[2])
            ->build($args[1]);
    }

    /**
     * Summary of build
     * @param array<string,SubDimensionDTO> $dimensions
     * @throws \Exception
     * @return void
     */
    public function build(...$args): Builder
    {
        if (!$this->query)
            throw new \Exception("Querry acc not found!");

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
            $this->joinInParent($dim->table, $dim->parent);
        }

        $grouping = array_unique($grouping);

        if (!empty($grouping))
            $this->query->groupBy($grouping);

        if (!empty($cols))
            $this->query->addSelect($cols);

        return $this->query;
    }


    /**
     * Summary of filter
     * @param string $table
     * @param array<string,array> $filters
     * @return void
     */
    private function joinInParent(string $sub_dimension_table, ?string $parent = null): void
    {

        if ($parent) {
            $this->joinInTable(
                $sub_dimension_table,
                $this->dimensions[$parent]
            );
            return;
        }

        // dd($sub_dimension_table);
        foreach ($this->dimensions as $dim_table_name => $dim) {

            $this->joinInTable($sub_dimension_table, $dim);

        }
    }



}