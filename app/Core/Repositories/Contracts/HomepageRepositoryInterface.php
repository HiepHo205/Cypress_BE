<?php

namespace App\Core\Repositories\Contracts;

interface HomepageRepositoryInterface extends BaseRepositoryInterface
{
    public function getSection(string $key): array;

    public function updateSection(string $key, array $data): array;
}
