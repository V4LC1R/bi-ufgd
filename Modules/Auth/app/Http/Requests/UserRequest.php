<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ou lógica de auth se precisar
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'password' => [
                'required',
                Password::min(12)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'email' => ['required', 'email', 'unique:users,email']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please give a name',

            'password.required' => 'The user needs a password',
            'password.min' => 'To be safe, use at least 12 characters',

            'email.required' => 'User needs an email to access reports',
            'email.email' => 'Please provide a valid email address',
        ];
    }

}