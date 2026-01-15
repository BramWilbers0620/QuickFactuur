<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\EncryptsBackups;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BackupFiles extends Command
{
    use EncryptsBackups;

    protected $signature = 'backup:files
                            {--disk=local : Storage disk to backup from}
                            {--no-encrypt : Disable encryption even if enabled in config}';

    protected $description = 'Create an encrypted backup of uploaded files (invoices, logos)';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $shouldEncrypt = $this->isEncryptionEnabled() && !$this->option('no-encrypt');

        $this->info('Starting file backup...');

        if ($shouldEncrypt) {
            $this->info('Encryption: enabled');
        } else {
            $this->warn('Encryption: disabled');
        }

        // Directories to backup
        $directories = ['invoices', 'logos', 'quotes'];
        $filesToBackup = [];

        foreach ($directories as $dir) {
            if (Storage::disk($disk)->exists($dir)) {
                $files = Storage::disk($disk)->allFiles($dir);
                foreach ($files as $file) {
                    $filesToBackup[] = $file;
                }
                $this->line("  Found " . count($files) . " files in {$dir}/");
            }
        }

        if (empty($filesToBackup)) {
            $this->info('No files to backup.');
            return Command::SUCCESS;
        }

        $this->info("Backing up " . count($filesToBackup) . " files...");

        try {
            $backupPath = $this->createZipBackup($filesToBackup, $timestamp, $disk, $shouldEncrypt);

            if (!$backupPath) {
                $this->error('Backup failed.');
                return Command::FAILURE;
            }

            // Get file size
            $size = Storage::disk($disk)->size($backupPath);
            $sizeFormatted = $this->formatBytes($size);

            Log::info('File backup created', [
                'path' => $backupPath,
                'size' => $sizeFormatted,
                'file_count' => count($filesToBackup),
                'encrypted' => $shouldEncrypt,
            ]);

            $this->info("Backup created: {$backupPath} ({$sizeFormatted})");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('File backup failed', [
                'error' => $e->getMessage(),
            ]);

            $this->error("Backup failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    private function createZipBackup(array $files, string $timestamp, string $disk, bool $encrypt): ?string
    {
        $extension = $encrypt ? '.zip' . $this->getEncryptedExtension() : '.zip';
        $backupFileName = "files_backup_{$timestamp}{$extension}";
        $backupDir = config('backup.paths.files', 'backups/files');
        $backupPath = "{$backupDir}/{$backupFileName}";

        // Ensure backup directory exists
        Storage::disk($disk)->makeDirectory($backupDir);

        // Create zip in temp location first
        $tempZipPath = storage_path("app/{$backupDir}/temp_{$timestamp}.zip");

        $zip = new ZipArchive();

        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip file: {$tempZipPath}");
        }

        $addedCount = 0;
        foreach ($files as $file) {
            $fullPath = Storage::disk($disk)->path($file);

            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, $file);
                $addedCount++;
            }
        }

        $zip->close();

        $this->line("  Added {$addedCount} files to archive.");

        // Read the zip and optionally encrypt
        $content = file_get_contents($tempZipPath);
        @unlink($tempZipPath);

        if ($encrypt) {
            $this->line('  Encrypting backup...');
            $content = $this->encryptData($content);
        }

        Storage::disk($disk)->put($backupPath, $content);

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
