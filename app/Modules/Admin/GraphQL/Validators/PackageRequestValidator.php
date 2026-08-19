<?php

namespace App\Modules\Admin\GraphQL\Validators;

use Exception;

class PackageRequestValidator
{
    public static function validate(array $input): void
    {
        if (empty(trim($input['plan_id'] ?? ''))) {
            throw new Exception('Plan is required.');
        }

        $duration = (int) ($input['duration_days'] ?? 0);

        if (!in_array($duration, [30, 60, 90], true)) {
            throw new Exception('Duration must be one of: 30, 60, 90 days.');
        }

        if (empty(trim($input['full_name'] ?? ''))) {
            throw new Exception('Full name is required.');
        }

        if (empty(trim($input['email'] ?? ''))) {
            throw new Exception('Email is required.');
        }

        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email.');
        }

        if (empty(trim($input['phone'] ?? ''))) {
            throw new Exception('Phone is required.');
        }
    }
}
