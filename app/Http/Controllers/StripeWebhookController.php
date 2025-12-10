<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use App\Models\User;

class StripeWebhookController extends CashierController
{
    /**
     * Handle customer subscription deleted.
     */
    public function handleCustomerSubscriptionDeleted(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                Log::info('Subscription cancelled for user: ' . $user->email);

                // Reset trial_ends_at if subscription is cancelled
                $user->update([
                    'trial_ends_at' => null,
                ]);
            }
        }

        return $this->successMethod();
    }

    /**
     * Handle invoice payment failed.
     */
    public function handleInvoicePaymentFailed(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                Log::warning('Payment failed for user: ' . $user->email, [
                    'invoice_id' => $payload['data']['object']['id'] ?? 'unknown',
                    'amount' => $payload['data']['object']['amount_due'] ?? 0,
                ]);

                // Optionally send email notification about failed payment
                // Mail::to($user->email)->queue(new PaymentFailedMail($user));
            }
        }

        return $this->successMethod();
    }

    /**
     * Handle customer subscription updated.
     */
    public function handleCustomerSubscriptionUpdated(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;
        $status = $payload['data']['object']['status'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                Log::info('Subscription updated for user: ' . $user->email, [
                    'status' => $status,
                    'plan' => $payload['data']['object']['items']['data'][0]['price']['id'] ?? 'unknown',
                ]);
            }
        }

        return $this->successMethod();
    }

    /**
     * Handle charge refunded.
     */
    public function handleChargeRefunded(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

        if ($stripeCustomerId) {
            $user = User::where('stripe_id', $stripeCustomerId)->first();

            if ($user) {
                Log::info('Charge refunded for user: ' . $user->email, [
                    'amount' => $payload['data']['object']['amount_refunded'] ?? 0,
                ]);
            }
        }

        return $this->successMethod();
    }
}
