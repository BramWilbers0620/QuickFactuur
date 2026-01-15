<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {--disk=local : Storage disk to save backup to}';

    protected $description = 'Create a database backup';

    public function handle(): int
    {
        $driver = DB::connection()->getDriverName();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $disk = $this->option('disk');

        $this->info("Starting database backup ({$driver})...");

        try {
            $backupPath = match ($driver) {
                'sqlite' => $this->backupSqlite($timestamp),
                'mysql', 'mariadb' => $this->backupMysql($timestamp),
                'pgsql' => $this->backupPostgres($timestamp),
                default => throw new \RuntimeException("Unsupported database driver: {$driver}"),
            };

            if (!$backupPath) {
                $this->error('Backup failed.');
                return Command::FAILURE;
            }

            // Get file size for logging
            $size = Storage::disk($disk)->size($backupPath);
            $sizeFormatted = $this->formatBytes($size);

            Log::info('Database backup created', [
                'path' => $backupPath,
                'size' => $sizeFormatted,
                'driver' => $driver,
            ]);

            $this->info("Backup created: {$backupPath} ({$sizeFormatted})");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Database backup failed', [
                'error' => $e->getMessage(),
                'driver' => $driver,
            ]);

            $this->error("Backup failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    private function backupSqlite(string $timestamp): ?string
    {
        $dbPath = config('database.connections.sqlite.database');

        if (!file_exists($dbPath)) {
            throw new \RuntimeException("SQLite database not found: {$dbPath}");
        }

        $backupFileName = "backup_{$timestamp}.sqlite";
        $backupPath = "backups/database/{$backupFileName}";

        // Ensure directory exists
        Storage::disk('local')->makeDirectory('backups/database');

        // Copy the SQLite file
        $content = file_get_contents($dbPath);
        Storage::disk('local')->put($backupPath, $content);

        return $backupPath;
    }

    private function backupMysql(string $timestamp): ?string
    {
        $config = config('database.connections.mysql');
        $backupFileName = "backup_{$timestamp}.sql";
        $backupPath = "backups/database/{$backupFileName}";
        $tempFile = storage_path("app/{$backupPath}");

        // Ensure directory exists
        Storage::disk('local')->makeDirectory('backups/database');

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port']),
            escapeshellarg($config['username']),
            escapeshellarg($config['password']),
            escapeshellarg($config['database']),
            escapeshellarg($tempFile)
        );

        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException('mysqldump failed: ' . implode("\n", $output));
        }

        return $backupPath;
    }

    private function backupPostgres(string $timestamp): ?string
    {
        $config = config('database.connections.pgsql');
        $backupFileName = "backup_{$timestamp}.sql";
        $backupPath = "backups/database/{$backupFileName}";
        $tempFile = storage_path("app/{$backupPath}");

        // Ensure directory exists
        Storage::disk('local')->makeDirectory('backups/database');

        // Set password via environment variable for security
        putenv("PGPASSWORD={$config['password']}");

        // Build pg_dump command
        $command = sprintf(
            'pg_dump --host=%s --port=%s --username=%s --dbname=%s --file=%s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port']),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            escapeshellarg($tempFile)
        );

        exec($command . ' 2>&1', $output, $returnCode);

        // Clear password from environment
        putenv('PGPASSWORD');

        if ($returnCode !== 0) {
            throw new \RuntimeException('pg_dump failed: ' . implode("\n", $output));
        }

        return $backupPath;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
