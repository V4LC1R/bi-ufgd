<?php

namespace App\Modules\Querry\Http\DTOs;

use Illuminate\Support\Collection;

class PreSqlDTO
{
    public string $connectionName;

    /** @var DimensionDTO[] */
    public array $dimensions = [];

    /** @var DimensionDTO[] */
    public array $subDimensions = [];

    public FactDTO $fact = null;

    public function __construct(array $data)
    {
        $this->connectionName = $data['connectionName'] ?? '';

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

    public function toArray(): array
    {
        return [
            'connectionName' => $this->connectionName,
            'dimensions' => array_map(fn (DimensionDTO $d) => $d->toArray(), $this->dimensions),
            'sub-dimension' => array_map(fn (DimensionDTO $sd) => $sd->toArray(), $this->subDimensions),
            'fact' => $this->fact ? $this->fact->toArray() : null,
        ];
    }
}
