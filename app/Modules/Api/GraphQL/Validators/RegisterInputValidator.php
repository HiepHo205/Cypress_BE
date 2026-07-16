<?php

namespace App\Modules\Api\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

class RegisterInputValidator extends Validator
{
    public function rules(): array
    {
        dd($this->args);

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password_confirmation.same' =>
                'Password confirmation does not match.',
        ];
    }
}
