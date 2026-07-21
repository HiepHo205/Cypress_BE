<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\Repositories\Contracts\AuthRepositoryInterface;
use App\Core\Repositories\Eloquent\AuthRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
    }
}
