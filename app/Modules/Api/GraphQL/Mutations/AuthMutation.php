<?php

namespace App\Modules\Api\GraphQL\Mutations;

use App\Core\Services\Auth\AuthService;
use Exception;

class AuthMutation
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(mixed $root, array $args): array
    {
        try {
            return $this->authService->register($args);
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'user' => null,
            ];
        }
    }
}
