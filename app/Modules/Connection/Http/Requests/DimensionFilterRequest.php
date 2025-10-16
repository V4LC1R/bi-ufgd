<?php

namespace App\Modules\Connection\Http\Requests;

use App\Modules\Connection\Http\DTOs\DimensionFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class DimensionFilterRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     * Para agora, vamos permitir a todos.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Retorna as regras de validação que se aplicam à requisição.
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'perPage' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sortBy' => ['sometimes', 'string', 'max:255'],
            'sortDirection' => ['sometimes', 'string', 'in:asc,desc'],
            'filters' => ['sometimes', 'array'],
            'filters.*.column' => ['required_with:filters', 'string'],
            'filters.*.operator' => ['required_with:filters', 'string', 'in:eq,neq,like,gt,lt,gte,lte'],
            'filters.*.value' => ['nullable'],
        ];
    }

    public function toDTO()
    {
        return DimensionFilterDTO::from($this->all());
    }
}