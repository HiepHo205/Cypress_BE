<?php

return [
   'route' => [
        'middleware' => [
            \Illuminate\Http\Middleware\HandleCors::class,
            'api',
            \Nuwave\Lighthouse\Http\Middleware\AcceptJson::class,
        ],
        'prefix' => '',
        'domain' => 'api.cypresshub.com',
    ],
    'guards' => [
        'web' => 'web',
        'api' => 'api',
    ],
    'query_cache' => [
        'enable' => false,
        'mode' => 'store',
        'ttl' => 60,
    ],
    'cache' => [
        'enable' => false,
    ],
];
