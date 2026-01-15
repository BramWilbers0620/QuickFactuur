<?php

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Mark Overdue Invoices Command', function () {
    it('marks unpaid invoices past due date as overdue', function () {
        $overdueInvoice = Invoice::factory()->create([
            'status' => 'verzonden',
            'due_date' => now()->subDays(5),
        ]);

        $currentInvoice = Invoice::factory()->create([
            'status' => 'verzonden',
            'due_date' => now()->addDays(5),
        ]);

        $this->artisan('invoices:mark-overdue')
            ->assertExitCode(0);

        expect($overdueInvoice->fresh()->status)->toBe('te_laat');
        expect($currentInvoice->fresh()->status)->toBe('verzonden');
    });

    it('does not change paid invoices', function () {
        $paidInvoice = Invoice::factory()->paid()->create([
            'due_date' => now()->subDays(5),
        ]);

        $this->artisan('invoices:mark-overdue')
            ->assertExitCode(0);

        expect($paidInvoice->fresh()->status)->toBe('betaald');
    });

    it('does not change concept invoices', function () {
        $conceptInvoice = Invoice::factory()->create([
            'status' => 'concept',
            'due_date' => now()->subDays(5),
        ]);

        $this->artisan('invoices:mark-overdue')
            ->assertExitCode(0);

        expect($conceptInvoice->fresh()->status)->toBe('concept');
    });

    it('outputs count of marked invoices', function () {
        Invoice::factory()->count(3)->create([
            'status' => 'verzonden',
            'due_date' => now()->subDays(5),
        ]);

        $this->artisan('invoices:mark-overdue')
            ->expectsOutputToContain('3')
            ->assertExitCode(0);
    });
});
