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
    public ?array $order = null;

    public array $alias = [];

    public ?string $table_alias =  null;

    public function __construct(array $data)
    {
        $this->table = $data['table'] ?? '';
        $this->columns = $data['columns'] ?? [];
        $this->filter = $data['filter'] ?? [];
        $this->order = $data['order'] ?? [];
        $this->alias = $data['alias'] ?? [];
        $this->table_alias = $data['tableAlias'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'table'      => $this->table,
            'columns'    => $this->columns,
            'filter'     => $this->filter,
            'order'      => $this->order,
            'alias'      => $this->alias,
            'tableAlias' => $this->table_alias
        ];
    }
}