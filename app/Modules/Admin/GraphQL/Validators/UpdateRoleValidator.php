<?php

namespace App\Modules\Admin\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateRoleValidator extends Validator
{
    public function rules(): array
    {
        $id = (int) $this->arg('id');

        return [
            'id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($id, 'id'),
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Role ID is required.',
            'id.integer' => 'Role ID must be an integer.',
            'id.exists' => 'The selected role does not exist.',

            'name.required' => 'Role name is required.',
            'name.unique' => 'Role name already exists.',

            'description.string' => 'Description must be a string.',
        ];
    }
}
