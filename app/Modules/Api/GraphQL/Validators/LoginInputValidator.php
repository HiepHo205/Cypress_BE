<?php

namespace App\Modules\Api\GraphQL\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginInputValidator
{
    public function validate(array $data): array
    {
        $validator = Validator::make(
            $data,
            [
                'email' => ['required', 'email'],

                'password' => ['required', 'string', 'min:6'],
            ],
            [
                'email.required' => 'Email is required.',
                'email.email' => 'Email format is invalid.',

                'password.required' => 'Password is required.',
                'password.min' => 'Password must be at least 6 characters.',
            ],
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
