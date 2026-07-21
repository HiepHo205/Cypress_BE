<?php

namespace App\Modules\Admin\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateRoleValidator extends Validator
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'exists:roles,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ];
    }
}
