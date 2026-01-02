<?php

namespace App\Console\Commands;

use App\Models\Quote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireQuotes extends Command
{
    protected $signature = 'quotes:expire';

    protected $description = 'Mark expired quotes as verlopen';

    public function handle(): int
    {
        $count = Quote::where('valid_until', '<', now())
            ->whereNotIn('status', ['verlopen', 'geaccepteerd', 'afgewezen'])
            ->update(['status' => 'verlopen']);

        if ($count > 0) {
            Log::info("Marked {$count} quotes as expired");
            $this->info("Marked {$count} quote(s) as expired.");
        } else {
            $this->info('No quotes to expire.');
        }

        return Command::SUCCESS;
    }
}
