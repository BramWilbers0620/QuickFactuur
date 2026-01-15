<?php

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

describe('Dashboard Controller', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'trial_ends_at' => now()->addDays(14),
            'email_verified_at' => now(), // Required for dashboard access
        ]);
    });

    describe('index', function () {
        it('shows dashboard for authenticated user with access', function () {
            $response = $this->actingAs($this->user)->get(route('dashboard'));

            $response->assertStatus(200);
            $response->assertViewHas('stats');
        });

        it('redirects unauthenticated users', function () {
            $response = $this->get(route('dashboard'));
            $response->assertRedirect(route('login'));
        });

        it('redirects users without access to billing', function () {
            $expiredUser = User::factory()->create([
                'trial_ends_at' => now()->subDays(1),
                'email_verified_at' => now(),
            ]);

            $response = $this->actingAs($expiredUser)->get(route('dashboard'));
            $response->assertRedirect(route('billing'));
        });

        it('calculates correct invoice statistics', function () {
            // Create invoices for this user
            Invoice::factory()->count(3)->create([
                'user_id' => $this->user->id,
                'status' => 'betaald',
                'total' => 100.00,
            ]);
            Invoice::factory()->count(2)->create([
                'user_id' => $this->user->id,
                'status' => 'verzonden',
                'total' => 50.00,
            ]);

            // Create invoice for other user (should not be counted)
            Invoice::factory()->create(['total' => 1000.00]);

            // Clear cache to force recalculation
            Cache::forget("dashboard_stats_{$this->user->id}");

            $response = $this->actingAs($this->user)->get(route('dashboard'));

            $response->assertStatus(200);
            $stats = $response->viewData('stats');

            expect($stats['totalInvoices'])->toBe(5);
            expect($stats['paidInvoices'])->toBe(3);
            expect($stats['pendingInvoices'])->toBe(2);
        });

        it('calculates correct quote statistics', function () {
            Quote::factory()->count(2)->create([
                'user_id' => $this->user->id,
                'status' => 'geaccepteerd',
            ]);
            Quote::factory()->create([
                'user_id' => $this->user->id,
                'status' => 'verzonden',
            ]);

            // Clear cache
            Cache::forget("dashboard_stats_{$this->user->id}");

            $response = $this->actingAs($this->user)->get(route('dashboard'));

            $stats = $response->viewData('stats');
            expect($stats['totalQuotes'])->toBe(3);
        });

        it('calculates correct customer count', function () {
            Customer::factory()->count(5)->create(['user_id' => $this->user->id]);
            Customer::factory()->create(); // Other user's customer

            // Clear cache
            Cache::forget("dashboard_stats_{$this->user->id}");

            $response = $this->actingAs($this->user)->get(route('dashboard'));

            $stats = $response->viewData('stats');
            expect($stats['totalCustomers'])->toBe(5);
        });

        it('shows recent invoices', function () {
            Invoice::factory()->count(10)->create(['user_id' => $this->user->id]);

            // Clear cache
            Cache::forget("dashboard_stats_{$this->user->id}");

            $response = $this->actingAs($this->user)->get(route('dashboard'));

            $stats = $response->viewData('stats');
            expect($stats['recentInvoices'])->toHaveCount(5); // Should limit to 5
        });
    });
});
