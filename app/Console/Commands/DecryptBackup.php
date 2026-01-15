<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\EncryptsBackups;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DecryptBackup extends Command
{
    use EncryptsBackups;

    protected $signature = 'backup:decrypt
                            {file : The encrypted backup file path (relative to storage/app)}
                            {--output= : Output path for decrypted file (defaults to same name without .enc)}
                            {--disk=local : Storage disk}';

    protected $description = 'Decrypt an encrypted backup file';

    public function handle(): int
    {
        $file = $this->argument('file');
        $disk = $this->option('disk');

        // Validate file exists
        if (!Storage::disk($disk)->exists($file)) {
            $this->error("File not found: {$file}");
            return Command::FAILURE;
        }

        // Check if file is encrypted
        $encExtension = $this->getEncryptedExtension();
        if (!str_ends_with($file, $encExtension)) {
            $this->warn("File does not appear to be encrypted (no {$encExtension} extension)");

            if (!$this->confirm('Continue anyway?')) {
                return Command::FAILURE;
            }
        }

        // Determine output path
        $outputPath = $this->option('output');
        if (!$outputPath) {
            // Remove .enc extension
            $outputPath = str_ends_with($file, $encExtension)
                ? substr($file, 0, -strlen($encExtension))
                : $file . '.decrypted';
        }

        $this->info("Decrypting: {$file}");
        $this->info("Output: {$outputPath}");

        try {
            // Read encrypted content
            $encryptedContent = Storage::disk($disk)->get($file);

            // Decrypt
            $decryptedContent = $this->decryptData($encryptedContent);

            // Write decrypted content
            Storage::disk($disk)->put($outputPath, $decryptedContent);

            $size = Storage::disk($disk)->size($outputPath);
            $sizeFormatted = $this->formatBytes($size);

            Log::info('Backup decrypted', [
                'source' => $file,
                'output' => $outputPath,
                'size' => $sizeFormatted,
            ]);

            $this->info("Decryption successful: {$outputPath} ({$sizeFormatted})");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Backup decryption failed', [
                'file' => $file,
                'error' => $e->getMessage(),
            ]);

            $this->error("Decryption failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
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
