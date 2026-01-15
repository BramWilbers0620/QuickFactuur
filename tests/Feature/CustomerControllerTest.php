<?php

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Customer Controller', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'trial_ends_at' => now()->addDays(14),
        ]);
    });

    describe('index', function () {
        it('shows customer list for authenticated user', function () {
            Customer::factory()->count(3)->create(['user_id' => $this->user->id]);
            Customer::factory()->create(); // Other user's customer

            $response = $this->actingAs($this->user)->get(route('customers.index'));

            $response->assertStatus(200);
            $response->assertViewHas('customers');
            expect($response->viewData('customers'))->toHaveCount(3);
        });

        it('redirects unauthenticated users', function () {
            $response = $this->get(route('customers.index'));
            $response->assertRedirect(route('login'));
        });

        it('searches customers by name', function () {
            Customer::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'ABC Bedrijf',
            ]);
            Customer::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'XYZ Company',
            ]);

            $response = $this->actingAs($this->user)
                ->get(route('customers.index', ['search' => 'ABC']));

            $response->assertStatus(200);
            expect($response->viewData('customers'))->toHaveCount(1);
        });
    });

    describe('create', function () {
        it('shows create form', function () {
            $response = $this->actingAs($this->user)->get(route('customers.create'));

            $response->assertStatus(200);
        });
    });

    describe('store', function () {
        it('creates a new customer', function () {
            $customerData = [
                'name' => 'Test Klant',
                'email' => 'klant@test.nl',
                'address' => 'Teststraat 1',
                'phone' => '0612345678',
                'vat_number' => 'NL123456789B01',
            ];

            $response = $this->actingAs($this->user)
                ->post(route('customers.store'), $customerData);

            $response->assertRedirect(route('customers.index'));
            $this->assertDatabaseHas('customers', [
                'user_id' => $this->user->id,
                'name' => 'Test Klant',
                'email' => 'klant@test.nl',
            ]);
        });

        it('validates required fields', function () {
            $response = $this->actingAs($this->user)
                ->post(route('customers.store'), []);

            $response->assertSessionHasErrors(['name']);
        });

        it('validates email format', function () {
            $response = $this->actingAs($this->user)
                ->post(route('customers.store'), [
                    'name' => 'Test Klant',
                    'email' => 'invalid-email',
                ]);

            $response->assertSessionHasErrors(['email']);
        });
    });

    describe('edit', function () {
        it('shows edit form for own customer', function () {
            $customer = Customer::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->get(route('customers.edit', $customer));

            $response->assertStatus(200);
            $response->assertViewHas('customer');
        });

        it('returns 403 for other users customer', function () {
            $otherUser = User::factory()->create();
            $customer = Customer::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->get(route('customers.edit', $customer));

            $response->assertStatus(403);
        });
    });

    describe('update', function () {
        it('updates own customer', function () {
            $customer = Customer::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->patch(route('customers.update', $customer), [
                    'name' => 'Nieuwe Naam',
                    'email' => 'nieuw@email.nl',
                ]);

            $response->assertRedirect(route('customers.index'));
            expect($customer->fresh()->name)->toBe('Nieuwe Naam');
        });

        it('cannot update other users customer', function () {
            $otherUser = User::factory()->create();
            $customer = Customer::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->patch(route('customers.update', $customer), [
                    'name' => 'Nieuwe Naam',
                ]);

            $response->assertStatus(403);
        });
    });

    describe('destroy', function () {
        it('soft deletes own customer', function () {
            $customer = Customer::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->delete(route('customers.destroy', $customer));

            $response->assertRedirect(route('customers.index'));
            $this->assertSoftDeleted('customers', ['id' => $customer->id]);
        });

        it('cannot delete other users customer', function () {
            $otherUser = User::factory()->create();
            $customer = Customer::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->delete(route('customers.destroy', $customer));

            $response->assertStatus(403);
        });
    });
});
