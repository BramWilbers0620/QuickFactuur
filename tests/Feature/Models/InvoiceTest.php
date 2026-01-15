<?php

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Invoice Model', function () {
    it('generates sequential invoice numbers', function () {
        $user = User::factory()->create(['invoice_prefix' => 'FAC']);

        $number1 = Invoice::generateNextNumber($user->id);
        expect($number1)->toBe('FAC0001');

        Invoice::factory()->create([
            'user_id' => $user->id,
            'invoice_number' => $number1,
        ]);

        $number2 = Invoice::generateNextNumber($user->id);
        expect($number2)->toBe('FAC0002');
    });

    it('uses custom prefix for invoice numbers', function () {
        $user = User::factory()->create(['invoice_prefix' => 'INV']);

        $number = Invoice::generateNextNumber($user->id);
        expect($number)->toBe('INV0001');
    });

    it('includes soft-deleted invoices in number generation', function () {
        $user = User::factory()->create(['invoice_prefix' => 'FAC']);

        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'invoice_number' => 'FAC0001',
        ]);

        $invoice->delete();

        $number = Invoice::generateNextNumber($user->id);
        expect($number)->toBe('FAC0002');
    });

    it('throws exception for non-existent user', function () {
        Invoice::generateNextNumber(99999);
    })->throws(RuntimeException::class, 'User with ID 99999 not found');

    it('correctly identifies overdue invoices', function () {
        $overdueInvoice = Invoice::factory()->overdue()->create();
        $currentInvoice = Invoice::factory()->sent()->create();
        $paidInvoice = Invoice::factory()->paid()->create();

        expect($overdueInvoice->isOverdue())->toBeTrue();
        expect($currentInvoice->isOverdue())->toBeFalse();
        expect($paidInvoice->isOverdue())->toBeFalse();
    });

    it('marks invoice as sent', function () {
        $invoice = Invoice::factory()->create(['status' => 'concept']);

        $invoice->markAsSent();

        expect($invoice->fresh()->status)->toBe('verzonden');
        expect($invoice->fresh()->sent_at)->not->toBeNull();
    });

    it('marks invoice as paid', function () {
        $invoice = Invoice::factory()->sent()->create();

        $invoice->markAsPaid();

        expect($invoice->fresh()->status)->toBe('betaald');
        expect($invoice->fresh()->paid_at)->not->toBeNull();
    });

    it('returns correct status label', function () {
        $invoice = Invoice::factory()->create(['status' => 'verzonden']);
        expect($invoice->status_label)->toBe('Verzonden');

        $invoice->status = 'betaald';
        expect($invoice->status_label)->toBe('Betaald');
    });

    it('formats total correctly', function () {
        $invoice = Invoice::factory()->create(['total' => 1234.56]);
        expect($invoice->formatted_total)->toBe('€1.234,56');
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        expect($invoice->user)->toBeInstanceOf(User::class);
        expect($invoice->user->id)->toBe($user->id);
    });
});
