<?php

namespace App\Modules\Querry\Http\DTOs;

class FactColumnDTO
{
    public string $name;

    /** @var string[] operações: avg, sum, list etc */
    public array $aggregates = [];

    /** @var array|null filtros por operação */
    public ?array $filter = null;

    public ?array $as = null;
    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->as = $data['as'] ?? null;
        $this->aggregates = $data['agg'] ?? [];
        $this->filter = $data['filter'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'aggregates'  => $this->aggregates,
            'filter'     => $this->filter
        ];
    }
}
