<?php

namespace App\Modules\Querry\Http\DTOs;

class FactColumnDTO
{
    public string $name;

    /** @var string[] operações: avg, sum, list etc */
    public array $aggregates = [];

    /** @var string[] operações: avg, sum, list etc */
    public array $linear = [];

    /** @var array|null filtros por operação */
    public array $filter = [];

    public array $alias = [];

    /** @var array mapeamento de agregação para direção (ex: ["avg" => "desc"]) */
    public array $order = []; // <-- Propriedade adicionada


    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->alias = $data['alias'] ?? [];
        $this->aggregates = $data['aggregates'] ?? [];
        $this->linear = $data['linear'] ?? [];
        $this->filter = $data['filter'] ?? [];
        $this->order = $data['order'] ?? []; //

    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'aggregates' => $this->aggregates,
            'linear' => $this->linear,
            'filter' => $this->filter,
            'alias' => $this->alias,
            'order' => $this->order
        ];
    }
}

