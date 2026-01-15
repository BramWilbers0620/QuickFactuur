<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Billing Controller', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'trial_ends_at' => now()->addDays(14),
        ]);
    });

    describe('index', function () {
        it('shows billing page for authenticated user', function () {
            $response = $this->actingAs($this->user)->get(route('billing'));

            $response->assertStatus(200);
        });

        it('redirects unauthenticated users', function () {
            $response = $this->get(route('billing'));
            $response->assertRedirect(route('login'));
        });
    });

    describe('subscribe', function () {
        it('validates plan selection', function () {
            $response = $this->actingAs($this->user)
                ->post(route('subscribe'), [
                    'plan' => 'invalid_plan',
                ]);

            $response->assertSessionHasErrors(['plan']);
        });

        it('validates plan is required', function () {
            $response = $this->actingAs($this->user)
                ->post(route('subscribe'), []);

            $response->assertSessionHasErrors(['plan']);
        });

        it('accepts valid monthly plan', function () {
            $response = $this->actingAs($this->user)
                ->post(route('subscribe'), [
                    'plan' => 'monthly',
                ]);

            // Should not have validation errors (may redirect or error due to Stripe not being configured)
            $response->assertSessionDoesntHaveErrors(['plan']);
        });

        it('accepts valid yearly plan', function () {
            $response = $this->actingAs($this->user)
                ->post(route('subscribe'), [
                    'plan' => 'yearly',
                ]);

            // Should not have validation errors
            $response->assertSessionDoesntHaveErrors(['plan']);
        });
    });

    describe('cancel', function () {
        it('requires authentication', function () {
            $response = $this->post(route('subscription.cancel'));
            $response->assertRedirect(route('login'));
        });

        it('returns error when user has no subscription', function () {
            $response = $this->actingAs($this->user)
                ->post(route('subscription.cancel'));

            $response->assertRedirect(route('billing'));
            $response->assertSessionHas('error');
        });
    });

    describe('success', function () {
        it('requires session_id parameter', function () {
            $response = $this->actingAs($this->user)
                ->get(route('billing.success'));

            // Without session_id, should have validation error
            $response->assertStatus(302);
        });
    });
});
