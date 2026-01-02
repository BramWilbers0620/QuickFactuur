<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'invoices:mark-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Mark invoices as overdue when past due date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = Invoice::whereIn('status', ['concept', 'verzonden'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->update(['status' => 'te_laat']);

        if ($count > 0) {
            Log::info("Marked {$count} invoices as overdue");
            $this->info("Marked {$count} invoices as overdue.");
        } else {
            $this->info("No invoices to mark as overdue.");
        }

        return Command::SUCCESS;
    }
}
