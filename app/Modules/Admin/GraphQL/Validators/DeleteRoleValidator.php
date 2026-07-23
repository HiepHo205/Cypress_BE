<?php

namespace App\Modules\Admin\GraphQL\Validators;

use Nuwave\Lighthouse\Validation\Validator;

class DeleteRoleValidator extends Validator
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'id.required' => 'Role ID is required.',
            'id.exists' => 'The selected role does not exist.',
        ];
    }
}