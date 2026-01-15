<?php

use App\Models\Invoice;
use App\Models\User;
use App\Mail\PaymentReminderMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

describe('Send Payment Reminders Command', function () {
    beforeEach(function () {
        Mail::fake();
    });

    it('sends reminders for invoices overdue by specified days', function () {
        $user = User::factory()->create();

        // Invoice exactly 7 days overdue
        $overdueInvoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'te_laat',
            'due_date' => now()->subDays(7),
            'customer_email' => 'customer@test.nl',
        ]);

        // Invoice 8 days overdue (should not be sent)
        Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'te_laat',
            'due_date' => now()->subDays(8),
            'customer_email' => 'other@test.nl',
        ]);

        $this->artisan('invoices:send-reminders', ['--days' => 7])
            ->assertExitCode(0);

        Mail::assertQueued(PaymentReminderMail::class, 1);
        Mail::assertQueued(PaymentReminderMail::class, function ($mail) use ($overdueInvoice) {
            return $mail->invoice->id === $overdueInvoice->id;
        });
    });

    it('does not send reminders for invoices without email', function () {
        $user = User::factory()->create();

        Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'te_laat',
            'due_date' => now()->subDays(7),
            'customer_email' => null,
        ]);

        $this->artisan('invoices:send-reminders', ['--days' => 7])
            ->assertExitCode(0);

        Mail::assertNothingQueued();
    });

    it('respects dry-run option', function () {
        $user = User::factory()->create();

        Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'te_laat',
            'due_date' => now()->subDays(7),
            'customer_email' => 'customer@test.nl',
        ]);

        $this->artisan('invoices:send-reminders', ['--days' => 7, '--dry-run' => true])
            ->assertExitCode(0);

        Mail::assertNothingQueued();
    });

    it('uses default 7 days if not specified', function () {
        $user = User::factory()->create();

        Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => 'te_laat',
            'due_date' => now()->subDays(7),
            'customer_email' => 'customer@test.nl',
        ]);

        $this->artisan('invoices:send-reminders')
            ->assertExitCode(0);

        Mail::assertQueued(PaymentReminderMail::class, 1);
    });
});
