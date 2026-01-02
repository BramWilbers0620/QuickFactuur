<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's company information.
     */
    public function updateCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:50',
            // Dutch KVK number is exactly 8 digits
            'company_kvk' => ['nullable', 'string', 'max:20', 'regex:/^(\d{8})?$/'],
            // IBAN format: 2 letters, 2 digits, then alphanumeric (NL91ABNA0417164300)
            'company_iban' => ['nullable', 'string', 'max:34', 'regex:/^([A-Z]{2}\d{2}[A-Z0-9]{1,30})?$/'],
            // Invoice/quote prefix: 2-10 uppercase letters only
            'invoice_prefix' => ['nullable', 'string', 'min:2', 'max:10', 'regex:/^[A-Z]+$/'],
            'quote_prefix' => ['nullable', 'string', 'min:2', 'max:10', 'regex:/^[A-Z]+$/'],
            // Default payment terms
            'default_payment_terms' => ['nullable', 'string', 'in:direct,14,30,60'],
        ], [
            'company_kvk.regex' => 'Het KVK-nummer moet uit 8 cijfers bestaan.',
            'company_iban.regex' => 'Voer een geldig IBAN-nummer in (bijv. NL91ABNA0417164300).',
            'invoice_prefix.regex' => 'De factuur prefix mag alleen hoofdletters bevatten.',
            'invoice_prefix.min' => 'De factuur prefix moet minimaal 2 tekens zijn.',
            'quote_prefix.regex' => 'De offerte prefix mag alleen hoofdletters bevatten.',
            'quote_prefix.min' => 'De offerte prefix moet minimaal 2 tekens zijn.',
        ]);

        $request->user()->update($validated);

        return Redirect::route('profile.edit')->with('status', 'company-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            // Cancel any active Stripe subscription before deleting user
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancelNow();
                Log::info('Subscription cancelled on account deletion', ['user_id' => $user->id]);
            }

            // Delete Stripe customer if exists
            if ($user->stripe_id) {
                try {
                    $user->asStripeCustomer()->delete();
                    Log::info('Stripe customer deleted', ['user_id' => $user->id, 'stripe_id' => $user->stripe_id]);
                } catch (\Exception $e) {
                    Log::warning('Could not delete Stripe customer', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error during account deletion cleanup', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        Auth::logout();

        $user->delete();

        Log::info('User account deleted', ['user_id' => $user->id]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
