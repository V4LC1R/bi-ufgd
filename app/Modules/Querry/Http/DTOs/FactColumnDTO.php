<?php

namespace App\Modules\Querry\Http\DTOs;

class FactColumnDTO
{
    public string $name;

    /** @var string[] operações: avg, sum, list etc */
    public array $operations = [];

    /** @var array|null filtros por operação */
    public ?array $filter = null;

    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->operations = $data['operation'] ?? [];
        $this->filter = $data['filter'] ?? null;
    }
}
