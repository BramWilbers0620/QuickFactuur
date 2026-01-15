<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Audit Logging
    |--------------------------------------------------------------------------
    |
    | This option controls whether audit logging is enabled for the application.
    | When disabled, no audit logs will be created.
    |
    */

    'enabled' => env('AUDIT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Log Console Commands
    |--------------------------------------------------------------------------
    |
    | When true, audit logs will be created even when running console commands
    | like migrations, seeders, or scheduled tasks. Useful for tracking
    | automated changes.
    |
    */

    'log_console' => env('AUDIT_LOG_CONSOLE', false),

    /*
    |--------------------------------------------------------------------------
    | Retention Period
    |--------------------------------------------------------------------------
    |
    | The number of days to keep audit logs before they can be pruned.
    | Use the artisan command audit:prune to clean up old logs.
    |
    */

    'retention_days' => env('AUDIT_RETENTION_DAYS', 365),

    /*
    |--------------------------------------------------------------------------
    | Events to Log
    |--------------------------------------------------------------------------
    |
    | Specify which events should be logged. Available events:
    | created, updated, deleted, restored
    |
    */

    'events' => [
        'created',
        'updated',
        'deleted',
        'restored',
    ],
];
