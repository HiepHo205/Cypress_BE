<?php

namespace App\Core\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface FooterRepositoryInterface
{
    public function getFooter(): Collection;

    public function updateBranding(array $data);

    public function updateNewsletter(array $data);

    public function updateSocial(array $data);

    public function updateNavigation(array $data);

    public function updateBottomBar(array $data);
}
