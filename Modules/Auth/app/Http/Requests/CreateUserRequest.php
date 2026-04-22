<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Enum;
use Modules\Auth\Data\CreateUserData;
use Modules\Auth\Enums\RoleEnum;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $this->user()->can(
            'create',
            [
                $user,
                RoleEnum::from($this->input('role'))
            ]
        );
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
            'email' => ['required', 'email'],
            'role' => ['required', new Enum(RoleEnum::class)]
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

    public function toDto(): CreateUserData
    {
        return CreateUserData::from($this->validated());
    }

}