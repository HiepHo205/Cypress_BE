<?php

namespace App\Core\Repositories\Contracts;

interface HeaderRepositoryInterface
{
    public function getHeader();
    public function createMenu(array $data);
    public function updateMenu(int $id, array $data);
    public function updateLogo(array $image);
    public function getLogo();
    public function deleteMenu(int $id);
}
