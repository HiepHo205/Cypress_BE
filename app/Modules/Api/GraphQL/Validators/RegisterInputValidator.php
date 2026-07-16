<?php

namespace App\Modules\Api\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

class RegisterInputValidator extends Validator
{
    public function rules(): array
    {
        return [
            'input.name' => ['required', 'string', 'max:255'],
            'input.email' => ['required', 'email'],
            'input.password' => ['required', 'string', 'min:6'],
            'input.password_confirmation' => [
                'required',
                'same:input.password',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'input.name.required' => 'Name is required.',
            'input.email.required' => 'Email is required.',
            'input.email.email' => 'Invalid email format.',
            'input.password.required' => 'Password is required.',
            'input.password.min' => 'Password must be at least 6 characters.',
            'input.password_confirmation.required' =>
                'Confirm password is required.',
            'input.password_confirmation.same' =>
                'Password confirmation does not match.',
        ];
    }
}
