<?php

namespace App\Modules\Querry\Http\DTOs;

class DimensionDTO
{
    public string $table;

    /** @var string[] */
    public array $columns = [];

    /** @var array|null */
    public ?array $filter = null;

    /** @var array|null */
    public bool $group = false;

    public array $alias = [];

    public ?string $table_alias = null;

    public function __construct(array $data)
    {
        $this->table = $data['table'] ?? '';
        $this->columns = $data['columns'] ?? [];
        $this->filter = $data['filter'] ?? [];
        $this->group = $data['group'] ?? false;
        $this->alias = $data['alias'] ?? [];
        $this->table_alias = $data['tableAlias'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'table' => $this->table,
            'columns' => $this->columns,
            'filter' => $this->filter,
            'order' => $this->group,
            'alias' => $this->alias,
            'tableAlias' => $this->table_alias
        ];
    }
}