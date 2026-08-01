<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\LighthouseServiceProvider;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureGraphQLGuard();
    }

    private function configureGraphQLGuard(): void
    {
        $host = request()->getHost();
        if (
            $host === config('app.admin_url', 'admin.cypresshub.com') ||
            $host === '127.0.0.1' ||
            $host === 'localhost'
        ) {
            config([
                'lighthouse.route.middleware' => [
                    'web',
                    \Nuwave\Lighthouse\Http\Middleware\AcceptJson::class,
                ],
            ]);
            config(['lighthouse.guard' => 'web']);
        } else {
            config([
                'lighthouse.route.middleware' => [
                    'api',
                    \Nuwave\Lighthouse\Http\Middleware\AcceptJson::class,
                ],
            ]);
            config(['lighthouse.guard' => 'api']);
        }
    }
}
