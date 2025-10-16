<?php

namespace App\Modules\Connection\Http\DTOs;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Sometimes;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;



class DimensionFilterDTO extends Data
{
    /** @var FilterCriterioDTO[] */
    #[Sometimes()] // Valida cada item do array usando as regras do FilterCriterionDTO
    #[DataCollectionOf(FilterCriterioDTO::class)] // Converte o array em objetos DTO
    public readonly array $filters;

    public function __construct(
        #[Sometimes, Min(1)]
        public readonly int $page = 1,

        #[Sometimes, Min(1)]
        public readonly int $perPage = 15,

        #[Sometimes]
        public readonly string $sortBy = 'id',

        #[Sometimes, In(['asc', 'desc'])]
        public readonly string $sortDirection = 'asc',

        public readonly string $table,

        // A propriedade $filters é declarada acima com seus atributos
        Optional|array $filters = [],
    ) {
        $this->filters = $filters;
    }
}