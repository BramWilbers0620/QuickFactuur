<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Encryption
    |--------------------------------------------------------------------------
    |
    | When enabled, all backups will be encrypted using AES-256-GCM encryption.
    | This ensures that backup files are protected even if storage is compromised.
    |
    | The encryption key should be a base64-encoded 32-byte key.
    | Generate one with: php -r "echo base64_encode(random_bytes(32));"
    |
    */

    'encryption_enabled' => env('BACKUP_ENCRYPTION_ENABLED', false),

    'encryption_key' => env('BACKUP_ENCRYPTION_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Backup Retention
    |--------------------------------------------------------------------------
    |
    | The number of days to keep backups before they are automatically deleted
    | by the backup:cleanup command.
    |
    */

    'retention_days' => env('BACKUP_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Backup Paths
    |--------------------------------------------------------------------------
    |
    | The directories where backups are stored.
    |
    */

    'paths' => [
        'database' => 'backups/database',
        'files' => 'backups/files',
    ],
];
