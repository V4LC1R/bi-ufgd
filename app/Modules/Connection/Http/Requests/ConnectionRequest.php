<?php

namespace App\Modules\Connection\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConnectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ou lógica de auth se precisar
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'connection.host' => ['required', 'string'],
            'connection.port' => ['required', 'string'],
            'connection.password' => ['required', 'string'],
            'connection.user' => ['required', 'string'],
            'connection.type' => ['required', 'string', 'in:mysql,pgsql,sqlite,sqlsrv'],

            'tables' => ['required', 'array', 'min:1'],
            'tables.*.type' => ['required', 'string', 'in:dimension,sub-dimension,fact'],
            'tables.*.name' => ['required', 'string'],
            'tables.*.alias' => ['required', 'string'],
            'tables.*.columns' => ['required', 'array', 'min:1'],

            // cada campo da columns deve ser string
            'tables.*.columns.*' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'connection.host.required' => 'O host da conexão é obrigatório.',
            'tables.*.type.in' => 'O tipo da tabela deve ser dimension, sub-dimension ou fact.',
            'tables.*.columns.*.string' => 'Cada campo da columns deve ser uma string válida.'
        ];
    }
}
