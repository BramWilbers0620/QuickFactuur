<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupBackups extends Command
{
    protected $signature = 'backup:cleanup
                            {--days=30 : Delete backups older than this many days}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Remove old backup files';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up backups older than {$days} days...");

        if ($dryRun) {
            $this->warn('[DRY RUN MODE]');
        }

        $deletedCount = 0;
        $freedSpace = 0;

        // Cleanup database backups
        $dbResult = $this->cleanupDirectory('backups/database', $cutoffDate, $dryRun);
        $deletedCount += $dbResult['count'];
        $freedSpace += $dbResult['size'];

        // Cleanup file backups
        $filesResult = $this->cleanupDirectory('backups/files', $cutoffDate, $dryRun);
        $deletedCount += $filesResult['count'];
        $freedSpace += $filesResult['size'];

        $freedFormatted = $this->formatBytes($freedSpace);

        if ($deletedCount === 0) {
            $this->info('No old backups to delete.');
        } else {
            $action = $dryRun ? 'Would delete' : 'Deleted';
            $this->info("{$action} {$deletedCount} backup(s), freeing {$freedFormatted}");

            if (!$dryRun) {
                Log::info('Backup cleanup completed', [
                    'deleted_count' => $deletedCount,
                    'freed_space' => $freedFormatted,
                    'older_than_days' => $days,
                ]);
            }
        }

        return Command::SUCCESS;
    }

    private function cleanupDirectory(string $directory, Carbon $cutoffDate, bool $dryRun): array
    {
        $result = ['count' => 0, 'size' => 0];

        if (!Storage::disk('local')->exists($directory)) {
            return $result;
        }

        $files = Storage::disk('local')->files($directory);

        foreach ($files as $file) {
            $lastModified = Storage::disk('local')->lastModified($file);
            $fileDate = Carbon::createFromTimestamp($lastModified);

            if ($fileDate->lt($cutoffDate)) {
                $size = Storage::disk('local')->size($file);

                if ($dryRun) {
                    $this->line("  [DRY RUN] Would delete: {$file}");
                } else {
                    Storage::disk('local')->delete($file);
                    $this->line("  Deleted: {$file}");
                }

                $result['count']++;
                $result['size'] += $size;
            }
        }

        return $result;
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
