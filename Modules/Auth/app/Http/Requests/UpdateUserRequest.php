<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Enum;
use Modules\Auth\Data\UpdateUserData;
use Modules\Auth\Enums\RoleEnum;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $this->user()->can(
            'update',
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
                'nullable',
                Password::min(12)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],

            'email' => ['required', 'email'],

            'role' => ['required', new Enum(RoleEnum::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please give a name',

            'password.min' => 'To be safe, use at least 12 characters',

            'email.required' => 'User needs an email to access reports',
            'email.email' => 'Please provide a valid email address',
        ];
    }

    public function toDto(): UpdateUserData
    {
        return UpdateUserData::from([
            ...$this->validated(),
            'id' => $this->route('user')->id ?? $this->route('user'),
        ]);
    }
}