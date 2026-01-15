<?php

use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Expire Quotes Command', function () {
    it('marks quotes past valid_until as expired', function () {
        $expiredQuote = Quote::factory()->create([
            'status' => 'verzonden',
            'valid_until' => now()->subDays(5),
        ]);

        $validQuote = Quote::factory()->create([
            'status' => 'verzonden',
            'valid_until' => now()->addDays(5),
        ]);

        $this->artisan('quotes:expire')
            ->assertExitCode(0);

        expect($expiredQuote->fresh()->status)->toBe('verlopen');
        expect($validQuote->fresh()->status)->toBe('verzonden');
    });

    it('does not change accepted quotes', function () {
        $acceptedQuote = Quote::factory()->accepted()->create([
            'valid_until' => now()->subDays(5),
        ]);

        $this->artisan('quotes:expire')
            ->assertExitCode(0);

        expect($acceptedQuote->fresh()->status)->toBe('geaccepteerd');
    });

    it('does not change rejected quotes', function () {
        $rejectedQuote = Quote::factory()->create([
            'status' => 'afgewezen',
            'valid_until' => now()->subDays(5),
        ]);

        $this->artisan('quotes:expire')
            ->assertExitCode(0);

        expect($rejectedQuote->fresh()->status)->toBe('afgewezen');
    });

    it('does not change already expired quotes', function () {
        $alreadyExpired = Quote::factory()->expired()->create();

        $this->artisan('quotes:expire')
            ->assertExitCode(0);

        expect($alreadyExpired->fresh()->status)->toBe('verlopen');
    });

    it('does not change concept quotes', function () {
        $conceptQuote = Quote::factory()->create([
            'status' => 'concept',
            'valid_until' => now()->subDays(5),
        ]);

        $this->artisan('quotes:expire')
            ->assertExitCode(0);

        expect($conceptQuote->fresh()->status)->toBe('concept');
    });
});
