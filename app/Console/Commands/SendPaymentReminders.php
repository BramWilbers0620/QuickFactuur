<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'invoices:send-reminders
                            {--days=7 : Send reminder for invoices overdue by this many days}
                            {--dry-run : Show what would be sent without actually sending}';

    protected $description = 'Send payment reminder emails for overdue invoices';

    public function handle(): int
    {
        $daysOverdue = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("Looking for invoices overdue by {$daysOverdue} days...");

        // Find invoices that are exactly N days overdue (sent/overdue status, have email, have due_date)
        $overdueDate = now()->subDays($daysOverdue)->startOfDay();

        $invoices = Invoice::whereIn('status', ['verzonden', 'te_laat'])
            ->whereNotNull('customer_email')
            ->whereNotNull('due_date')
            ->whereDate('due_date', $overdueDate)
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('No invoices found that are exactly ' . $daysOverdue . ' days overdue.');
            return Command::SUCCESS;
        }

        $this->info("Found {$invoices->count()} invoice(s) to send reminders for.");

        $sentCount = 0;
        $failedCount = 0;

        foreach ($invoices as $invoice) {
            // Skip if due_date is somehow null (defensive check)
            if (!$invoice->due_date) {
                continue;
            }

            $actualDaysOverdue = now()->diffInDays($invoice->due_date);

            if ($dryRun) {
                $this->line("  [DRY RUN] Would send reminder for {$invoice->invoice_number} to {$invoice->customer_email} ({$actualDaysOverdue} days overdue)");
                $sentCount++;
                continue;
            }

            try {
                Mail::to($invoice->customer_email)->send(new PaymentReminderMail($invoice, $actualDaysOverdue));

                Log::info('Payment reminder sent', [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_email' => substr($invoice->customer_email, 0, 3) . '***',
                    'days_overdue' => $actualDaysOverdue,
                ]);

                $this->line("  Sent reminder for {$invoice->invoice_number} to {$invoice->customer_email}");
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Payment reminder failed', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);

                $this->error("  Failed to send reminder for {$invoice->invoice_number}: {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info("Summary: {$sentCount} sent, {$failedCount} failed");

        return $failedCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
