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

    public function __construct(array $data)
    {
        $this->table = $data['table'] ?? '';
        $this->columns = $data['columns'] ?? [];
        $this->filter = $data['filter'] ?? null;
        $this->order = $data['order'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'table'   => $this->table,
            'columns' => $this->columns,
            'filter'  => $this->filter,
            'order'   => $this->order,
        ];
    }
}
