<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

describe('Profile Controller', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    describe('edit', function () {
        it('shows profile edit form', function () {
            $response = $this->actingAs($this->user)->get(route('profile.edit'));

            $response->assertStatus(200);
            $response->assertViewHas('user');
        });

        it('redirects unauthenticated users', function () {
            $response = $this->get(route('profile.edit'));
            $response->assertRedirect(route('login'));
        });
    });

    describe('update', function () {
        it('updates user profile', function () {
            $response = $this->actingAs($this->user)
                ->patch(route('profile.update'), [
                    'name' => 'Nieuwe Naam',
                    'email' => 'nieuw@email.nl',
                ]);

            $response->assertRedirect(route('profile.edit'));
            expect($this->user->fresh()->name)->toBe('Nieuwe Naam');
            expect($this->user->fresh()->email)->toBe('nieuw@email.nl');
        });

        it('validates email format', function () {
            $response = $this->actingAs($this->user)
                ->patch(route('profile.update'), [
                    'name' => 'Test',
                    'email' => 'invalid-email',
                ]);

            $response->assertSessionHasErrors(['email']);
        });

        it('validates unique email', function () {
            $otherUser = User::factory()->create(['email' => 'taken@email.nl']);

            $response = $this->actingAs($this->user)
                ->patch(route('profile.update'), [
                    'name' => 'Test',
                    'email' => 'taken@email.nl',
                ]);

            $response->assertSessionHasErrors(['email']);
        });

        it('requires email reverification on email change', function () {
            $this->user->email_verified_at = now();
            $this->user->save();

            $response = $this->actingAs($this->user)
                ->patch(route('profile.update'), [
                    'name' => 'Test',
                    'email' => 'nieuwe@email.nl',
                ]);

            expect($this->user->fresh()->email_verified_at)->toBeNull();
        });
    });

    describe('updateCompany', function () {
        it('updates company profile', function () {
            $response = $this->actingAs($this->user)
                ->patch(route('profile.company.update'), [
                    'company_name' => 'Mijn Bedrijf BV',
                    'company_address' => 'Hoofdstraat 1, 1234 AB Amsterdam',
                    'company_phone' => '020-1234567',
                    'company_kvk' => '12345678',
                    'company_iban' => 'NL91ABNA0417164300',
                    'invoice_prefix' => 'INV',
                    'quote_prefix' => 'QUO',
                    'default_payment_terms' => '30',
                ]);

            $response->assertRedirect(route('profile.edit'));

            $user = $this->user->fresh();
            expect($user->company_name)->toBe('Mijn Bedrijf BV');
            expect($user->company_kvk)->toBe('12345678');
            expect($user->invoice_prefix)->toBe('INV');
            expect($user->default_payment_terms)->toBe('30');
        });

        it('validates payment terms', function () {
            $response = $this->actingAs($this->user)
                ->patch(route('profile.company.update'), [
                    'default_payment_terms' => 'invalid',
                ]);

            $response->assertSessionHasErrors(['default_payment_terms']);
        });

        it('validates prefix format', function () {
            $response = $this->actingAs($this->user)
                ->patch(route('profile.company.update'), [
                    'invoice_prefix' => 'TOOLONGPREFIX',
                ]);

            $response->assertSessionHasErrors(['invoice_prefix']);
        });
    });

    describe('destroy', function () {
        it('deletes user account with password confirmation', function () {
            $response = $this->actingAs($this->user)
                ->delete(route('profile.destroy'), [
                    'password' => 'password', // Default factory password
                ]);

            $response->assertRedirect('/');
            $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
        });

        it('requires correct password', function () {
            $response = $this->actingAs($this->user)
                ->from(route('profile.edit'))
                ->delete(route('profile.destroy'), [
                    'password' => 'wrong-password',
                ]);

            $response->assertSessionHasErrorsIn('userDeletion', 'password');
            $this->assertDatabaseHas('users', ['id' => $this->user->id]);
        });
    });
});
