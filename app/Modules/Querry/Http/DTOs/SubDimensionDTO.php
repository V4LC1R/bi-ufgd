<?php

namespace App\Modules\Querry\Http\DTOs;

class SubDimensionDTO extends DimensionDTO
{
    public ?string $parent;

   

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->parent = $data['parent'] ?? null;

    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'parent'=>$this->parent
            ]
        );
    }
}
