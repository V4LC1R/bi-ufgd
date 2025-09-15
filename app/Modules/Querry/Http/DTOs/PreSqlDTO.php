<?php

namespace App\Modules\Querry\Http\DTOs;

use Illuminate\Support\Collection;

class PreSqlDTO
{
    public string $database;

    /** @var DimensionDTO[] */
    public array $dimensions = [];

    /** @var DimensionDTO[] */
    public array $subDimensions = [];

    public ?FactDTO $fact = null;

    public function __construct(array $data)
    {
        $this->database = $data['database'] ?? '';

        // Mapeia dimensões
        if (!empty($data['dimensions'])) {
            foreach ($data['dimensions'] as $dim) {
                $this->dimensions[] = new DimensionDTO($dim);
            }
        }

        // Mapeia sub-dimensões
        if (!empty($data['sub-dimension'])) {
            foreach ($data['sub-dimension'] as $subDim) {
                $this->subDimensions[] = new DimensionDTO($subDim);
            }
        }

        // Mapeia fact
        if (!empty($data['fact'])) {
            $this->fact = new FactDTO($data['fact']);
        }
    }
}
