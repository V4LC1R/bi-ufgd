<?php

namespace App\Modules\Connection\Http\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class FilterCriterioDTO extends Data
{
    public function __construct(
            // As regras de validação agora vivem aqui!
        #[Rule('required|string')]
        public readonly string $column,

        #[In(['gt', 'lt', 'eq', 'LIKE', 'startWith', 'endWith', 'contains'])]
        public readonly string $operator,

        #[Nullable]
        public readonly mixed $value,
    ) {
    }
}