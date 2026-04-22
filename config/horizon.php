<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    */
    'domain' => env('HORIZON_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    */
    'path' => env('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    */
    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    */
    'prefix' => env('HORIZON_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_horizon:'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    | Sécurise l'accès à /horizon – admin seulement
    */
    'middleware' => ['web', 'auth', 'role:admin'],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    */
    'waits' => [
        'redis:default' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times (minutes)
    |--------------------------------------------------------------------------
    */
    'trim' => [
        'recent'        => 60,
        'pending'       => 60,
        'completed'     => 120,
        'recent_failed' => 10080,  // 7 jours
        'failed'        => 10080,
        'monitored'     => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Silenced Jobs
    |--------------------------------------------------------------------------
    */
    'silenced' => [],

    /*
    |--------------------------------------------------------------------------
    | Metrics
    |--------------------------------------------------------------------------
    */
    'metrics' => [
        'trim_snapshots' => [
            'job'  => 24,
            'queue' => 24,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    */
    'fast_termination' => false,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit (MB)
    |--------------------------------------------------------------------------
    */
    'memory_limit' => 128,

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    */
    'environments' => [

        'production' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue'      => ['default'],
                'balance'    => 'auto',
                'maxProcesses' => 5,
                'minProcesses' => 1,
                'maxTime'    => 0,
                'maxJobs'    => 0,
                'memory'     => 128,
                'tries'      => 3,
                'timeout'    => 60,
                'nice'       => 0,
            ],

            // Queue dédiée aux notifications (SMS + Email)
            'supervisor-notifications' => [
                'connection'   => 'redis',
                'queue'        => ['notifications'],
                'balance'      => 'simple',
                'maxProcesses' => 3,
                'minProcesses' => 1,
                'memory'       => 64,
                'tries'        => 3,
                'timeout'      => 30,
            ],

            // Queue dédiée à l'IA (OpenAI – plus lent)
            'supervisor-ai' => [
                'connection'   => 'redis',
                'queue'        => ['ai'],
                'balance'      => 'simple',
                'maxProcesses' => 2,
                'minProcesses' => 1,
                'memory'       => 128,
                'tries'        => 2,
                'timeout'      => 120,
            ],

            // Queue pour les emails marketing (alertes, newsletters)
            'supervisor-mail' => [
                'connection'   => 'redis',
                'queue'        => ['mail'],
                'balance'      => 'simple',
                'maxProcesses' => 2,
                'minProcesses' => 1,
                'memory'       => 64,
                'tries'        => 3,
                'timeout'      => 45,
            ],
        ],

        'local' => [
            'supervisor-local' => [
                'connection'   => 'redis',
                'queue'        => ['default', 'notifications', 'ai', 'mail'],
                'balance'      => 'simple',
                'maxProcesses' => 3,
                'memory'       => 128,
                'tries'        => 3,
            ],
        ],
    ],
];
