<?php

namespace App\Modules\Querry\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuerryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'database' => ['required', 'string'],

            // Dimensions
            'dimensions' => ['nullable', 'array'],
            'dimensions.*.table' => ['required_with:dimensions', 'string'],
            'dimensions.*.columns' => ['required_with:dimensions', 'array', 'min:1'],
            'dimensions.*.columns.*' => ['string'],
            'dimensions.*.filter' => ['nullable', 'array'],
            'dimensions.*.filter.*.op' => ['required_with:dimensions.*.filter', 'string'],
            'dimensions.*.filter.*.value' => ['nullable'],
            'dimensions.*.order' => ['nullable', 'array'],
            'dimensions.*.order.*' => ['in:asc,desc'],

            // Sub-dimensions
            'sub-dimension' => ['nullable', 'array'],
            'sub-dimension.*.table' => ['required_with:sub-dimension', 'string'],
            'sub-dimension.*.columns' => ['required_with:sub-dimension', 'array', 'min:1'],
            'sub-dimension.*.columns.*' => ['string'],
            'sub-dimension.*.filter' => ['nullable', 'array'],
            'sub-dimension.*.filter.*.op' => ['required_with:sub-dimension.*.filter', 'string'],
            'sub-dimension.*.filter.*.value' => ['nullable'],
            'sub-dimension.*.order' => ['nullable', 'array'],
            'sub-dimension.*.order.*' => ['in:asc,desc'],

            // Fact
            'fact' => ['required', 'array'],
            'fact.colunms' => ['required', 'array'],
            'fact.colunms.*' => ['required', 'array'],
            'fact.colunms.*.operation' => ['required', 'array', 'min:1'],
            'fact.colunms.*.operation.*' => ['string'], // aceita avg,sum,:list,:arg
            'fact.colunms.*.filter' => ['nullable', 'array'],
            'fact.colunms.*.filter.*.op' => ['required_with:fact.colunms.*.filter', 'string'],
            'fact.colunms.*.filter.*.value' => ['nullable'],
            'fact.colunms.*.filter.*.order' => ['nullable', 'in:asc,desc'],
        ];
    }

    public function messages(): array
    {
        return [
            'database.required' => 'O campo database é obrigatório.',

            'dimensions.*.table.required_with' => 'Cada dimensão precisa ter uma tabela.',
            'dimensions.*.columns.required_with' => 'Cada dimensão precisa ter colunas.',
            'dimensions.*.order.*.in' => 'A ordenação só pode ser asc ou desc.',

            'sub-dimension.*.table.required_with' => 'Cada sub-dimensão precisa ter uma tabela.',
            'sub-dimension.*.columns.required_with' => 'Cada sub-dimensão precisa ter colunas.',
            'sub-dimension.*.order.*.in' => 'A ordenação só pode ser asc ou desc.',

            'fact.colunms.required' => 'É obrigatório definir colunas no fact.',
            'fact.colunms.*.operation.required' => 'Cada coluna do fact precisa ter ao menos uma operação.',
            'fact.colunms.*.filter.*.order.in' => 'A ordenação no fact só pode ser asc ou desc.',
        ];
    }
}
