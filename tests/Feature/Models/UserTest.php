<?php

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('User Model', function () {
    it('detects active trial correctly', function () {
        $userWithTrial = User::factory()->create([
            'trial_ends_at' => now()->addDays(7),
        ]);

        $userWithExpiredTrial = User::factory()->create([
            'trial_ends_at' => now()->subDays(1),
        ]);

        $userWithoutTrial = User::factory()->create([
            'trial_ends_at' => null,
        ]);

        expect($userWithTrial->onGenericTrial())->toBeTrue();
        expect($userWithExpiredTrial->onGenericTrial())->toBeFalse();
        expect($userWithoutTrial->onGenericTrial())->toBeFalse();
    });

    it('admin always has active access', function () {
        $admin = User::factory()->create([
            'is_admin' => true,
            'trial_ends_at' => null,
        ]);

        expect($admin->hasActiveAccess())->toBeTrue();
        expect($admin->isAdmin())->toBeTrue();
    });

    it('user with trial has active access', function () {
        $user = User::factory()->create([
            'is_admin' => false,
            'trial_ends_at' => now()->addDays(7),
        ]);

        expect($user->hasActiveAccess())->toBeTrue();
    });

    it('user without trial or subscription has no access', function () {
        $user = User::factory()->create([
            'is_admin' => false,
            'trial_ends_at' => null,
        ]);

        expect($user->hasActiveAccess())->toBeFalse();
    });

    it('returns correct payment terms days', function () {
        $user = User::factory()->create(['default_payment_terms' => '14']);
        expect($user->getPaymentTermsDays())->toBe(14);

        $user->default_payment_terms = '30';
        expect($user->getPaymentTermsDays())->toBe(30);

        $user->default_payment_terms = '60';
        expect($user->getPaymentTermsDays())->toBe(60);

        $user->default_payment_terms = 'direct';
        expect($user->getPaymentTermsDays())->toBe(0);
    });

    it('returns default 30 days for invalid payment terms', function () {
        $user = User::factory()->create(['default_payment_terms' => null]);
        expect($user->getPaymentTermsDays())->toBe(30);

        $user->default_payment_terms = '-10';
        expect($user->getPaymentTermsDays())->toBe(30);

        $user->default_payment_terms = '500';
        expect($user->getPaymentTermsDays())->toBe(30);
    });

    it('has many invoices', function () {
        $user = User::factory()->create();
        Invoice::factory()->count(3)->create(['user_id' => $user->id]);

        expect($user->invoices)->toHaveCount(3);
        expect($user->invoices->first())->toBeInstanceOf(Invoice::class);
    });

    it('has many quotes', function () {
        $user = User::factory()->create();
        Quote::factory()->count(2)->create(['user_id' => $user->id]);

        expect($user->quotes)->toHaveCount(2);
        expect($user->quotes->first())->toBeInstanceOf(Quote::class);
    });

    it('uses default prefix when not set', function () {
        $user = User::factory()->create([
            'invoice_prefix' => null,
            'quote_prefix' => null,
        ]);

        $invoiceNumber = Invoice::generateNextNumber($user->id);
        expect($invoiceNumber)->toStartWith('FAC');

        $quoteNumber = Quote::generateNextNumber($user->id);
        expect($quoteNumber)->toStartWith('OFF');
    });
});
