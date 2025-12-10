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
            'company_kvk' => 'nullable|string|max:20',
            'company_iban' => 'nullable|string|max:50',
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
