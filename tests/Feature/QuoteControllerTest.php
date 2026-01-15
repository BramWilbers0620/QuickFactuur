<?php

use App\Models\Quote;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Quote Controller', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'trial_ends_at' => now()->addDays(14),
            'company_name' => 'Test Bedrijf',
            'company_address' => 'Teststraat 1',
            'quote_prefix' => 'OFF',
            'invoice_prefix' => 'FAC',
        ]);
    });

    describe('index', function () {
        it('shows quote list for authenticated user', function () {
            Quote::factory()->count(3)->create(['user_id' => $this->user->id]);
            Quote::factory()->create(); // Other user's quote

            $response = $this->actingAs($this->user)->get(route('quotes.index'));

            $response->assertStatus(200);
            $response->assertViewHas('quotes');
            expect($response->viewData('quotes'))->toHaveCount(3);
        });

        it('redirects unauthenticated users', function () {
            $response = $this->get(route('quotes.index'));
            $response->assertRedirect(route('login'));
        });
    });

    describe('create', function () {
        it('shows create form for user with access', function () {
            $response = $this->actingAs($this->user)->get(route('quotes.create'));

            $response->assertStatus(200);
            $response->assertViewHas('nextQuoteNumber');
            $response->assertViewHas('companyProfile');
        });

        it('redirects user without access to billing', function () {
            $expiredUser = User::factory()->create([
                'trial_ends_at' => now()->subDays(1),
            ]);

            $response = $this->actingAs($expiredUser)->get(route('quotes.create'));

            $response->assertRedirect(route('billing'));
        });

        it('generates correct next quote number', function () {
            Quote::factory()->create([
                'user_id' => $this->user->id,
                'quote_number' => 'OFF0001',
            ]);

            $response = $this->actingAs($this->user)->get(route('quotes.create'));

            expect($response->viewData('nextQuoteNumber'))->toBe('OFF0002');
        });
    });

    describe('updateStatus', function () {
        it('updates quote status', function () {
            $quote = Quote::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'concept',
            ]);

            $response = $this->actingAs($this->user)
                ->patch(route('quotes.status', $quote), [
                    'status' => 'verzonden',
                ]);

            $response->assertRedirect();
            expect($quote->fresh()->status)->toBe('verzonden');
        });

        it('validates status values', function () {
            $quote = Quote::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->patch(route('quotes.status', $quote), [
                    'status' => 'invalid_status',
                ]);

            $response->assertSessionHasErrors(['status']);
        });

        it('cannot update other users quote', function () {
            $otherUser = User::factory()->create();
            $quote = Quote::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->patch(route('quotes.status', $quote), [
                    'status' => 'verzonden',
                ]);

            $response->assertStatus(403);
        });
    });

    describe('convertToInvoice', function () {
        it('converts accepted quote to invoice', function () {
            $quote = Quote::factory()->accepted()->create([
                'user_id' => $this->user->id,
                'total' => 1234.56,
            ]);

            $response = $this->actingAs($this->user)
                ->post(route('quotes.convert', $quote));

            $response->assertRedirect(route('invoice.index'));

            // Check quote has a converted_invoice_id
            expect($quote->fresh()->converted_invoice_id)->not->toBeNull();

            // Check invoice was created
            $invoice = Invoice::where('user_id', $this->user->id)->first();
            expect($invoice)->not->toBeNull();
            expect((float) $invoice->total)->toBe(1234.56);
        });

        it('cannot convert other users quote', function () {
            $otherUser = User::factory()->create();
            $quote = Quote::factory()->accepted()->create([
                'user_id' => $otherUser->id,
            ]);

            $response = $this->actingAs($this->user)
                ->post(route('quotes.convert', $quote));

            $response->assertStatus(403);
        });
    });

    describe('download', function () {
        it('cannot download other users quote', function () {
            $otherUser = User::factory()->create();
            $quote = Quote::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->get(route('quotes.download', $quote));

            $response->assertStatus(403);
        });
    });
});
