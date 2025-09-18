<?php

namespace App\Modules\Querry\Http\DTOs;

class FactDTO
{
    /** @var FactColumnDTO[] */
    public array $columns = [];

    public string | int $limit=0;

    public function __construct(array $data)
    {
        $this->limit = $data["limit"] ?? 0;

        if (!empty($data['colunms'])) { // Preservando o typo do JSON "colunms"
            foreach ($data['colunms'] as $name => $colData) {
                $this->columns[$name] = new FactColumnDTO($name, $colData);
            }
        }
    }

    public function toArray(): array
    {
        $columns = [];
        foreach ($this->columns as $name => $column) {
            $columns[$name] = $column->toArray();
        }

        return [
            'colunms' => $columns,
            'limit'   => $this->limit,
        ];
    }
}
