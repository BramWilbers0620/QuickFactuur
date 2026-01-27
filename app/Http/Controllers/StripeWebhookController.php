<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use App\Models\User;
use App\Mail\PaymentFailedMail;

class StripeWebhookController extends CashierController
{
    /**
     * Handle customer subscription deleted.
     */
    public function handleCustomerSubscriptionDeleted(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        // Custom logging - wrapped in try-catch so it doesn't prevent parent from running
        try {
            $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

            if ($stripeCustomerId) {
                $user = User::where('stripe_id', $stripeCustomerId)->first();

                if ($user) {
                    Log::info('Subscription cancelled', ['user_id' => $user->id]);

                    // Reset trial_ends_at if subscription is cancelled
                    $user->update([
                        'trial_ends_at' => null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't prevent parent from running
            Log::error('Webhook handleCustomerSubscriptionDeleted custom logic failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // Always call parent to properly mark subscription as canceled in database
        // Let parent exceptions bubble up so Stripe knows to retry if it fails
        return parent::handleCustomerSubscriptionDeleted($payload);
    }

    /**
     * Handle invoice payment failed.
     */
    public function handleInvoicePaymentFailed(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

            if ($stripeCustomerId) {
                $user = User::where('stripe_id', $stripeCustomerId)->first();

                if ($user) {
                    $invoiceId = $payload['data']['object']['id'] ?? null;
                    $amount = $payload['data']['object']['amount_due'] ?? null;
                    $attemptCount = $payload['data']['object']['attempt_count'] ?? 1;

                    Log::warning('Payment failed', [
                        'user_id' => $user->id,
                        'invoice_id' => $invoiceId ?? 'unknown',
                        'amount' => $amount ?? 0,
                        'attempt_count' => $attemptCount,
                    ]);

                    // Send email notification about failed payment synchronously
                    // Using sendNow() to bypass queue since this is a critical notification
                    try {
                        Mail::to($user->email)->sendNow(new PaymentFailedMail($user, $invoiceId, $amount));
                    } catch (\Exception $mailException) {
                        Log::error('Failed to send payment failed email', [
                            'user_id' => $user->id,
                            'error' => $mailException->getMessage(),
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Webhook handleInvoicePaymentFailed failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // No parent method to call - this is custom handling only
        return $this->successMethod();
    }

    /**
     * Handle customer subscription updated.
     */
    public function handleCustomerSubscriptionUpdated(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        // Custom logging - wrapped in try-catch so it doesn't prevent parent from running
        try {
            $stripeCustomerId = $payload['data']['object']['customer'] ?? null;
            $status = $payload['data']['object']['status'] ?? null;

            if ($stripeCustomerId) {
                $user = User::where('stripe_id', $stripeCustomerId)->first();

                if ($user) {
                    // Safely get price ID from nested structure
                    $priceId = 'unknown';
                    if (isset($payload['data']['object']['items']['data'][0]['price']['id'])) {
                        $priceId = $payload['data']['object']['items']['data'][0]['price']['id'];
                    }

                    Log::info('Subscription updated', [
                        'user_id' => $user->id,
                        'status' => $status,
                        'plan' => $priceId,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't prevent parent from running
            Log::error('Webhook handleCustomerSubscriptionUpdated custom logic failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // Always call parent to sync subscription data
        // Let parent exceptions bubble up so Stripe knows to retry if it fails
        return parent::handleCustomerSubscriptionUpdated($payload);
    }

    /**
     * Handle charge refunded.
     */
    public function handleChargeRefunded(array $payload): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $stripeCustomerId = $payload['data']['object']['customer'] ?? null;

            if ($stripeCustomerId) {
                $user = User::where('stripe_id', $stripeCustomerId)->first();

                if ($user) {
                    $amountRefunded = $payload['data']['object']['amount_refunded'] ?? 0;

                    Log::info('Charge refunded', [
                        'user_id' => $user->id,
                        'amount' => $amountRefunded,
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Webhook handleChargeRefunded failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // No parent method to call - this is custom handling only
        return $this->successMethod();
    }
}
