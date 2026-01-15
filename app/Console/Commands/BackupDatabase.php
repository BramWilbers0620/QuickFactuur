<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\EncryptsBackups;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    use EncryptsBackups;

    protected $signature = 'backup:database
                            {--disk=local : Storage disk to save backup to}
                            {--no-encrypt : Disable encryption even if enabled in config}';

    protected $description = 'Create an encrypted database backup';

    public function handle(): int
    {
        $driver = DB::connection()->getDriverName();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $disk = $this->option('disk');
        $shouldEncrypt = $this->isEncryptionEnabled() && !$this->option('no-encrypt');

        $this->info("Starting database backup ({$driver})...");

        if ($shouldEncrypt) {
            $this->info('Encryption: enabled');
        } else {
            $this->warn('Encryption: disabled');
        }

        try {
            $backupPath = match ($driver) {
                'sqlite' => $this->backupSqlite($timestamp, $shouldEncrypt),
                'mysql', 'mariadb' => $this->backupMysql($timestamp, $shouldEncrypt),
                'pgsql' => $this->backupPostgres($timestamp, $shouldEncrypt),
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
                'encrypted' => $shouldEncrypt,
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

    private function backupSqlite(string $timestamp, bool $encrypt): ?string
    {
        $dbPath = config('database.connections.sqlite.database');

        if (!file_exists($dbPath)) {
            throw new \RuntimeException("SQLite database not found: {$dbPath}");
        }

        $extension = $encrypt ? '.sqlite' . $this->getEncryptedExtension() : '.sqlite';
        $backupFileName = "backup_{$timestamp}{$extension}";
        $backupPath = config('backup.paths.database', 'backups/database') . "/{$backupFileName}";

        // Ensure directory exists
        Storage::disk('local')->makeDirectory(config('backup.paths.database', 'backups/database'));

        // Read the SQLite file
        $content = file_get_contents($dbPath);

        // Encrypt if enabled
        if ($encrypt) {
            $content = $this->encryptData($content);
        }

        Storage::disk('local')->put($backupPath, $content);

        return $backupPath;
    }

    private function backupMysql(string $timestamp, bool $encrypt): ?string
    {
        $config = config('database.connections.mysql');
        $extension = $encrypt ? '.sql' . $this->getEncryptedExtension() : '.sql';
        $backupFileName = "backup_{$timestamp}{$extension}";
        $backupDir = config('backup.paths.database', 'backups/database');
        $backupPath = "{$backupDir}/{$backupFileName}";

        // Use a temp file without encryption extension for mysqldump
        $tempFileName = "backup_{$timestamp}.sql.tmp";
        $tempFile = storage_path("app/{$backupDir}/{$tempFileName}");

        // Ensure directory exists
        Storage::disk('local')->makeDirectory($backupDir);

        // Set password via environment variable for security (avoids exposure in process listings)
        putenv("MYSQL_PWD={$config['password']}");

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s > %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['port']),
            escapeshellarg($config['username']),
            escapeshellarg($config['database']),
            escapeshellarg($tempFile)
        );

        exec($command . ' 2>&1', $output, $returnCode);

        // Clear password from environment
        putenv('MYSQL_PWD');

        if ($returnCode !== 0) {
            @unlink($tempFile);
            throw new \RuntimeException('mysqldump failed: ' . implode("\n", $output));
        }

        // Read and optionally encrypt
        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        if ($encrypt) {
            $content = $this->encryptData($content);
        }

        Storage::disk('local')->put($backupPath, $content);

        return $backupPath;
    }

    private function backupPostgres(string $timestamp, bool $encrypt): ?string
    {
        $config = config('database.connections.pgsql');
        $extension = $encrypt ? '.sql' . $this->getEncryptedExtension() : '.sql';
        $backupFileName = "backup_{$timestamp}{$extension}";
        $backupDir = config('backup.paths.database', 'backups/database');
        $backupPath = "{$backupDir}/{$backupFileName}";

        // Use a temp file for pg_dump
        $tempFileName = "backup_{$timestamp}.sql.tmp";
        $tempFile = storage_path("app/{$backupDir}/{$tempFileName}");

        // Ensure directory exists
        Storage::disk('local')->makeDirectory($backupDir);

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
            @unlink($tempFile);
            throw new \RuntimeException('pg_dump failed: ' . implode("\n", $output));
        }

        // Read and optionally encrypt
        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        if ($encrypt) {
            $content = $this->encryptData($content);
        }

        Storage::disk('local')->put($backupPath, $content);

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
