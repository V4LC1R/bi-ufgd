<?php

namespace App\Modules\Connection\Http\Requests;

use App\Modules\Connection\Http\DTOs\DimensionFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class DimensionFilterRequest extends FormRequest
{
    /**
     * O Spatie agora lida com a criação do DTO.
     * O método 'rules' é opcional se todas as regras estiverem no DTO.
     */
    protected function dataClass(): string
    {
        return DimensionFilterDTO::class;
    }

    public function authorize(): bool
    {
        return true;
    }
}