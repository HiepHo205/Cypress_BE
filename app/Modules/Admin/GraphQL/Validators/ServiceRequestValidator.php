<?php

namespace App\Modules\Admin\GraphQL\Validators;

use Exception;

class ServiceRequestValidator
{
    public static function validate(
        array $input
    ): void {

        if (
            empty(
                trim(
                    $input['request_type'] ?? ''
                )
            )
        ) {
            throw new Exception(
                'Request type is required.'
            );
        }

        if (
            empty(
                trim(
                    $input['full_name'] ?? ''
                )
            )
        ) {
            throw new Exception(
                'Full name is required.'
            );
        }

        if (
            empty(
                trim(
                    $input['email'] ?? ''
                )
            )
        ) {
            throw new Exception(
                'Email is required.'
            );
        }

        if (
            !filter_var(
                $input['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {
            throw new Exception(
                'Invalid email format.'
            );
        }

        if (
            empty(
                trim(
                    $input['phone'] ?? ''
                )
            )
        ) {
            throw new Exception(
                'Phone is required.'
            );
        }

        switch (
            $input['request_type']
        ) {

            case 'startup_package':

                if (
                    empty(
                        trim(
                            $input['company_name'] ?? ''
                        )
                    )
                ) {
                    throw new Exception(
                        'Company name is required.'
                    );
                }

                break;

            case 'contact':

                if (
                    empty(
                        trim(
                            $input['message'] ?? ''
                        )
                    )
                ) {
                    throw new Exception(
                        'Message is required.'
                    );
                }

                break;

            case 'office_tour':
            case 'meeting_room':
            case 'marketing_service':
            case 'software_consulting':
            case 'operations_consulting':
            case 'ai_consulting':
            case 'financial_advisor':
                break;

            default:
                throw new Exception(
                    'Invalid request type.'
                );
        }
    }
}