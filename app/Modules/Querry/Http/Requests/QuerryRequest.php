<?php

namespace App\Modules\Querry\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuerryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'connectionName' => ['required', 'string'],
            'description' => ['required', 'string'],
            'fact' => ['required', 'array'],
            'fact.limit' => ['required', 'integer', 'min:0'],
            'fact.columns' => ['required', 'array', 'min:1'],

            'fact.columns.*.aggregates' => ['sometimes', 'array'],
            'fact.columns.*.aggregates.*' => ['string'],

            'fact.columns.*.linear' => ['sometimes', 'array'],
            'fact.columns.*.linear.*' => ['string'],

            'fact.columns.*.alias' => ['sometimes', 'array'],
            'fact.columns.*.alias.*' => ['string'],

            'fact.columns.*.order' => ['sometimes', 'array'],
            'fact.columns.*.order.*' => ['string', Rule::in(['asc', 'desc'])],

            'fact.columns.*.filter' => ['sometimes', 'array'],

            'dimensions' => ['sometimes', 'array'],
            'dimensions.*.table' => ['required_with:dimensions', 'string'],
            'dimensions.*.columns' => ['sometimes', 'array', 'min:1'],
            'dimensions.*.group' => ['sometimes', 'boolean'], // <-- REGRA ADICIONADA

            'sub-dimension' => ['sometimes', 'array'],
            'sub-dimension.*.table' => ['required_with:sub-dimension', 'string'],
            'sub-dimension.*.columns' => ['sometimes', 'array', 'min:1'],
            'sub-dimension.*.parent' => ['sometimes', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'connectionName.required' => 'O campo connectionName é obrigatório.',
            'connectionName.string' => 'O campo connectionName deve ser uma string.',

            'fact.required' => 'O campo fact é obrigatório.',
            'fact.array' => 'O campo fact deve ser um array.',

            'fact.limit.required' => 'O campo fact.limit é obrigatório.',
            'fact.limit.integer' => 'O campo fact.limit deve ser um número inteiro.',
            'fact.limit.min' => 'O campo fact.limit deve ser maior ou igual a 0.',

            'fact.columns.required' => 'O campo fact.columns é obrigatório.',
            'fact.columns.array' => 'O campo fact.columns deve ser um array.',
            'fact.columns.min' => 'O campo fact.columns deve conter no minimo 1 item.',

            'fact.columns.*.aggregates.array' => 'O campo agg deve ser um array.',
            'fact.columns.*.aggregates.*.string' => 'Cada item de agg deve ser uma string.',

            'fact.columns.*.linear.array' => 'O campo linear deve ser um array.',
            'fact.columns.*.linear.*.string' => 'Cada item de linear deve ser uma string.',

            'fact.columns.*.alias.array' => 'O campo alias deve ser um array.',
            'fact.columns.*.alias.*.string' => 'Cada item de alias deve ser uma string.',

            'fact.columns.*.order.array' => 'O campo order deve ser um array (objeto).',
            'fact.columns.*.order.*.string' => 'A direção de order deve ser uma string.',
            'fact.columns.*.order.*.in' => 'A direção de order deve ser apenas "asc" ou "desc".',

            'fact.columns.*.filter.array' => 'O campo filter deve ser um array.',

            'dimensions.array' => 'O campo dimensions deve ser um array.',
            'dimensions.*.table.required_with' => 'O campo table é obrigatório em dimensions.',
            'dimensions.*.table.string' => 'O campo table em dimensions deve ser uma string.',
            'dimensions.*.columns.required_with' => 'O campo columns é obrigatório em dimensions.',
            'dimensions.*.columns.array' => 'O campo columns em dimensions deve ser um array.',
            'dimensions.*.columns.min' => 'O campo columns em dimensions deve ter pelo menos 1 item.',

            'sub-dimension.array' => 'O campo sub-dimension deve ser um array.',
            'sub-dimension.*.table.required_with' => 'O campo table é obrigatório em sub-dimension.',
            'sub-dimension.*.table.string' => 'O campo table em sub-dimension deve ser uma string.',
            'sub-dimension.*.columns.required_with' => 'O campo columns é obrigatório em sub-dimension.',
            'sub-dimension.*.columns.array' => 'O campo columns em sub-dimension deve ser um array.',
            'sub-dimension.*.columns.min' => 'O campo columns em sub-dimension deve ter pelo menos 1 item.',
            'sub-dimension.*.parent.string' => 'O caminho pelo parent tem que ser string'
        ];
    }
}