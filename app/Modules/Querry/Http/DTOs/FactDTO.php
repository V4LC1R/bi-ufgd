<?php

namespace App\Modules\Querry\Http\DTOs;

class FactDTO
{
    /** @var FactColumnDTO[] */
    public array $columns = [];

    public function __construct(array $data)
    {
        if (!empty($data['colunms'])) { // Preservando o typo do JSON "colunms"
            foreach ($data['colunms'] as $name => $colData) {
                $this->columns[$name] = new FactColumnDTO($name, $colData);
            }
        }
    }
}
