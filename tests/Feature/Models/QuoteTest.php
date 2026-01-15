<?php

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Quote Model', function () {
    it('generates sequential quote numbers', function () {
        $user = User::factory()->create(['quote_prefix' => 'OFF']);

        $number1 = Quote::generateNextNumber($user->id);
        expect($number1)->toBe('OFF0001');

        Quote::factory()->create([
            'user_id' => $user->id,
            'quote_number' => $number1,
        ]);

        $number2 = Quote::generateNextNumber($user->id);
        expect($number2)->toBe('OFF0002');
    });

    it('uses custom prefix for quote numbers', function () {
        $user = User::factory()->create(['quote_prefix' => 'QUO']);

        $number = Quote::generateNextNumber($user->id);
        expect($number)->toBe('QUO0001');
    });

    it('includes soft-deleted quotes in number generation', function () {
        $user = User::factory()->create(['quote_prefix' => 'OFF']);

        $quote = Quote::factory()->create([
            'user_id' => $user->id,
            'quote_number' => 'OFF0001',
        ]);

        $quote->delete();

        $number = Quote::generateNextNumber($user->id);
        expect($number)->toBe('OFF0002');
    });

    it('throws exception for non-existent user', function () {
        Quote::generateNextNumber(99999);
    })->throws(RuntimeException::class, 'User with ID 99999 not found');

    it('correctly identifies expired quotes', function () {
        $expiredQuote = Quote::factory()->expired()->create();
        $validQuote = Quote::factory()->create(['valid_until' => now()->addDays(30)]);

        expect($expiredQuote->isExpired())->toBeTrue();
        expect($validQuote->isExpired())->toBeFalse();
    });

    it('can convert to invoice when status allows', function () {
        $quote = Quote::factory()->create(['status' => 'concept']);
        expect($quote->canConvertToInvoice())->toBeTrue();

        $quote->status = 'verzonden';
        expect($quote->canConvertToInvoice())->toBeTrue();

        $quote->status = 'geaccepteerd';
        expect($quote->canConvertToInvoice())->toBeTrue();

        $quote->status = 'afgewezen';
        expect($quote->canConvertToInvoice())->toBeFalse();

        $quote->status = 'verlopen';
        expect($quote->canConvertToInvoice())->toBeFalse();
    });

    it('cannot convert if already converted', function () {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $quote = Quote::factory()->create([
            'user_id' => $user->id,
            'status' => 'geaccepteerd',
            'converted_invoice_id' => $invoice->id,
        ]);

        expect($quote->canConvertToInvoice())->toBeFalse();
    });

    it('converts quote to invoice correctly', function () {
        $user = User::factory()->create([
            'invoice_prefix' => 'FAC',
            'default_payment_terms' => '14',
        ]);

        $quote = Quote::factory()->create([
            'user_id' => $user->id,
            'status' => 'verzonden',
            'customer_name' => 'Test Klant',
            'total' => 1210.00,
        ]);

        $invoice = $quote->convertToInvoice();

        expect($invoice)->toBeInstanceOf(Invoice::class);
        expect($invoice->customer_name)->toBe('Test Klant');
        expect($invoice->total)->toBe('1210.00');
        expect($invoice->invoice_number)->toBe('FAC0001');
        expect($invoice->status)->toBe('concept');
        expect((int) now()->startOfDay()->diffInDays($invoice->due_date->startOfDay()))->toBe(14);

        $quote->refresh();
        expect($quote->status)->toBe('geaccepteerd');
        expect($quote->converted_invoice_id)->toBe($invoice->id);
        expect($quote->accepted_at)->not->toBeNull();
    });

    it('returns correct status label', function () {
        $quote = Quote::factory()->create(['status' => 'verzonden']);
        expect($quote->status_label)->toBe('Verzonden');

        $quote->status = 'geaccepteerd';
        expect($quote->status_label)->toBe('Geaccepteerd');
    });

    it('formats total correctly', function () {
        $quote = Quote::factory()->create(['total' => 2500.00]);
        expect($quote->formatted_total)->toBe('€2.500,00');
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $quote = Quote::factory()->create(['user_id' => $user->id]);

        expect($quote->user)->toBeInstanceOf(User::class);
        expect($quote->user->id)->toBe($user->id);
    });

    it('can access converted invoice with soft deletes', function () {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);
        $quote = Quote::factory()->create([
            'user_id' => $user->id,
            'converted_invoice_id' => $invoice->id,
        ]);

        $invoice->delete();

        expect($quote->convertedInvoice)->not->toBeNull();
        expect($quote->convertedInvoice->id)->toBe($invoice->id);
    });
});
