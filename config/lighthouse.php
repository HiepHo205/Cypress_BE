<?php

return [
    'route' => [
        'middleware' => [
            'api',
            \Nuwave\Lighthouse\Http\Middleware\AcceptJson::class,
        ],
        'prefix' => 'graphql',
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
