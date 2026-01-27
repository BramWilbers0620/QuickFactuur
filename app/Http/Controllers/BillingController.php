<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;
use App\Mail\SubscriptionStartedMail;
use Illuminate\Support\Facades\Mail;

class BillingController extends Controller
{
    public function index()
    {
        return view('billing');
    }

    /**
     * Start Stripe Checkout sessie voor abonnement.
     */
    public function subscribe(Request $request)
    {
        $user = Auth::user();

        // Validate Stripe configuration
        if (empty(config('cashier.key')) || empty(config('cashier.secret'))) {
            Log::error('Stripe is not configured. Set STRIPE_KEY and STRIPE_SECRET in .env');
            return redirect()->back()
                ->with('error', 'Betalingen zijn momenteel niet beschikbaar. Neem contact op met support.');
        }

        $validated = $request->validate([
            'plan' => 'required|in:monthly,yearly',
        ]);

        $plan = $validated['plan'];

        // Prevent race condition with cache lock
        $lockKey = 'subscription_checkout_' . $user->id;
        $lock = Cache::lock($lockKey, 30);

        if (!$lock->get()) {
            return redirect()->back()
                ->with('error', 'Er wordt al een betaling verwerkt. Probeer het over een paar seconden opnieuw.');
        }

        try {
            // Stop als gebruiker al een betaald abonnement heeft
            if ($user->subscribed('default')) {
                return redirect()->route('dashboard')
                    ->with('error', 'Je hebt al een actief abonnement.');
            }

            // Zorg dat gebruiker een Stripe customer heeft
            $user->createOrGetStripeCustomer();

            // Selecteer Stripe prijs-ID
            $stripePrice = match ($plan) {
                'monthly' => config('services.stripe.plan_monthly'),
                'yearly'  => config('services.stripe.plan_yearly'),
            };

            // Controleer of prijs-ID is geconfigureerd
            if (empty($stripePrice)) {
                throw new \Exception('Stripe prijs-ID is niet geconfigureerd voor plan: ' . $plan);
            }

            // Maak Stripe Checkout sessie met meerdere betaalmethodes
            $checkout = $user->newSubscription('default', $stripePrice)
                ->allowPromotionCodes()
                ->checkout([
                    'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('billing') . '?cancelled=true',
                    'payment_method_types' => ['card', 'ideal', 'bancontact'],
                    'locale' => 'nl',
                    'billing_address_collection' => 'auto',
                ]);

            return redirect($checkout->url);

        } catch (\Exception $e) {
            Log::error('Checkout error', [
                'user_id' => $user->id,
                'plan' => $plan,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Er ging iets mis bij het starten van de betaling. Probeer het later opnieuw.');
        } finally {
            $lock->release();
        }
    }

    /**
     * Handle succesvolle Stripe Checkout.
     */
    public function success(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Verify the session with Stripe
        try {
            $stripe = new \Stripe\StripeClient(config('cashier.secret'));
            $session = $stripe->checkout->sessions->retrieve($validated['session_id']);

            // Verify this session belongs to this user's Stripe customer
            if ($session->customer !== $user->stripe_id) {
                Log::warning('Stripe session customer mismatch', [
                    'user_id' => $user->id,
                    'session_customer' => $session->customer,
                    'user_stripe_id' => $user->stripe_id,
                ]);
                return redirect()->route('billing')
                    ->with('error', 'Er ging iets mis bij het verifiëren van je betaling.');
            }

            // Check payment status
            // For async payment methods (iDEAL, Bancontact), payment_status may be 'unpaid'
            // while status is 'complete' - this means payment is processing
            if ($session->payment_status === 'paid') {
                // Payment confirmed immediately (card payments)
                Log::info('Checkout session paid', ['user_id' => $user->id]);
            } elseif ($session->status === 'complete' && $session->payment_status === 'unpaid') {
                // Async payment (iDEAL/Bancontact) - processing via webhook
                Log::info('Checkout session complete, payment processing', [
                    'user_id' => $user->id,
                    'payment_status' => $session->payment_status,
                ]);
                return redirect()->route('dashboard')
                    ->with('success', 'Je betaling wordt verwerkt. Je ontvangt een bevestiging zodra de betaling is voltooid.');
            } else {
                // Actual failure
                Log::warning('Checkout session not completed', [
                    'user_id' => $user->id,
                    'payment_status' => $session->payment_status,
                    'session_status' => $session->status,
                ]);
                return redirect()->route('billing')
                    ->with('error', 'Je betaling is niet voltooid. Probeer het opnieuw.');
            }
        } catch (\Exception $e) {
            Log::error('Stripe session verification failed', [
                'user_id' => $user->id,
                'session_id' => $validated['session_id'],
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('billing')
                ->with('error', 'Er ging iets mis bij het verifiëren van je betaling.');
        }

        // Verwijder trial_ends_at nu ze een betaald abonnement hebben
        if ($user->trial_ends_at) {
            $user->update(['trial_ends_at' => null]);
        }

        // Bepaal welk plan ze hebben gekozen
        $subscription = $user->subscription('default');
        $planLabel = 'Onbekend';

        if ($subscription) {
            $stripePrice = $subscription->stripe_price;
            if ($stripePrice === config('services.stripe.plan_monthly')) {
                $planLabel = config('services.stripe.plan_monthly_label');
            } elseif ($stripePrice === config('services.stripe.plan_yearly')) {
                $planLabel = config('services.stripe.plan_yearly_label');
            }

            // Stuur bevestigingsmail
            try {
                Mail::to($user->email)->queue(new SubscriptionStartedMail($user, $planLabel));
            } catch (\Exception $e) {
                Log::warning('Could not send subscription email', ['error' => $e->getMessage()]);
            }
        }

        Log::info('Subscription started via Checkout', ['user_id' => $user->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Je abonnement is gestart! Je kunt nu onbeperkt facturen maken.');
    }

    /**
     * Cancel the user's subscription.
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();

        if (!$user->subscribed('default')) {
            return redirect()->route('billing')
                ->with('error', 'Je hebt geen actief abonnement om op te zeggen.');
        }

        try {
            $user->subscription('default')->cancel();

            Log::info('Subscription cancelled', ['user_id' => $user->id]);

            return redirect()->route('dashboard')
                ->with('success', 'Je abonnement is opgezegd. Je hebt nog toegang tot het einde van je huidige factureringsperiode.');

        } catch (\Exception $e) {
            Log::error('Subscription cancellation error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Er ging iets mis bij het opzeggen van je abonnement. Probeer het later opnieuw.');
        }
    }
}
