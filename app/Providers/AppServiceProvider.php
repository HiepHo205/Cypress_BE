<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $adminViewsPath = app_path('Modules/Admin/Views');

        if (is_dir($adminViewsPath)) {
            View::addNamespace('admin', $adminViewsPath);
        }

        if ($this->app->runningInConsole()) {
            $this->printCustomServeUrls();
        }
    }

    protected function printCustomServeUrls(): void
    {
        $argv = request()->server('argv') ?: [];

        if (in_array('serve', $argv)) {
            $port = '80';

            foreach ($argv as $arg) {
                if (str_starts_with($arg, '--port=')) {
                    $port = substr($arg, 7);
                }
            }

            if ($port === '80') {
                $port = env('SERVER_PORT', '80');
            }

            $portSuffix = in_array($port, ['80', '443']) ? '' : ":{$port}";

            usleep(50000);

            echo "\n";
            echo "  \e[1;32m➜\e[0m  \e[1mAdmin Portal:\e[0m \e[4;36mhttp://admin.cypresshub.com{$portSuffix}\e[0m\n";
            echo "  \e[1;32m➜\e[0m  \e[1mAPI Gateway:\e[0m  \e[4;36mhttp://api.cypresshub.com{$portSuffix}\e[0m\n";
            echo "\n";
        }
    }
}
