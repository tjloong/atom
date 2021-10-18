<?php

return [
    'app_route_prefix' => 'app',
    'locales' => ['en'],

    'track_ref' => [
        'duration' => 7,
        'exclude_paths' => [
            'app/*',
            'login',
            'forgot-password',
            'reset-password',
        ],
    ],
    
    'seo' => [
        'title' => null,
        'description' => null,
        'image' => null,
        'jsonld' => null,
        'hreflang' => [],
        'canonical' => [],
        'exclude_paths' => [
            'app/*',
            'login',
            'forgot-password',
            'reset-password',
        ],
    ],

    'ga' => [
        'id' => '',
        'exclude_paths' => [
            'app/*',
            'login',
            'forgot-password',
            'reset-password',
        ],
    ],

    'gtm' => [
        'id' => '',
        'exclude_paths' => [
            'app/*',
            'login',
            'forgot-password',
            'reset-password',
        ],
    ],

    'fbpixel' => [
        'id' => '',
        'exclude_paths' => [
            'app/*',
            'login',
            'forgot-password',
            'reset-password',
        ],
    ],
];