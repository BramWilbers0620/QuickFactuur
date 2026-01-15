<?php

use App\Http\Controllers\StripeWebhookController;
use App\Mail\PaymentFailedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

describe('Stripe Webhook Controller', function () {
    beforeEach(function () {
        $this->controller = new StripeWebhookController();
    });

    describe('handleCustomerSubscriptionDeleted', function () {
        it('resets trial when subscription is cancelled', function () {
            $user = User::factory()->create([
                'stripe_id' => 'cus_test123',
                'trial_ends_at' => now()->addDays(7),
            ]);

            $payload = [
                'data' => [
                    'object' => [
                        'customer' => 'cus_test123',
                    ],
                ],
            ];

            $response = $this->controller->handleCustomerSubscriptionDeleted($payload);

            expect($response->getStatusCode())->toBe(200);
            expect($user->fresh()->trial_ends_at)->toBeNull();
        });

        it('handles missing customer gracefully', function () {
            $payload = [
                'data' => [
                    'object' => [
                        'customer' => 'cus_nonexistent',
                    ],
                ],
            ];

            $response = $this->controller->handleCustomerSubscriptionDeleted($payload);

            expect($response->getStatusCode())->toBe(200);
        });

        it('handles missing customer id in payload', function () {
            $payload = [
                'data' => [
                    'object' => [],
                ],
            ];

            $response = $this->controller->handleCustomerSubscriptionDeleted($payload);

            expect($response->getStatusCode())->toBe(200);
        });
    });

    describe('handleInvoicePaymentFailed', function () {
        it('sends payment failed email', function () {
            Mail::fake();

            $user = User::factory()->create([
                'stripe_id' => 'cus_test456',
                'email' => 'test@example.com',
            ]);

            $payload = [
                'data' => [
                    'object' => [
                        'customer' => 'cus_test456',
                        'id' => 'in_test123',
                        'amount_due' => 2999,
                    ],
                ],
            ];

            $response = $this->controller->handleInvoicePaymentFailed($payload);

            expect($response->getStatusCode())->toBe(200);

            Mail::assertQueued(PaymentFailedMail::class, function ($mail) use ($user) {
                return $mail->user->id === $user->id
                    && $mail->stripeInvoiceId === 'in_test123'
                    && $mail->amount === 2999;
            });
        });

        it('handles missing user gracefully', function () {
            Mail::fake();

            $payload = [
                'data' => [
                    'object' => [
                        'customer' => 'cus_nonexistent',
                        'id' => 'in_test123',
                    ],
                ],
            ];

            $response = $this->controller->handleInvoicePaymentFailed($payload);

            expect($response->getStatusCode())->toBe(200);
            Mail::assertNothingQueued();
        });
    });

    describe('handleCustomerSubscriptionUpdated', function () {
        it('logs subscription update', function () {
            $user = User::factory()->create([
                'stripe_id' => 'cus_test789',
            ]);

            $payload = [
                'data' => [
                    'object' => [
                        'customer' => 'cus_test789',
                        'status' => 'active',
                        'items' => [
                            'data' => [
                                ['price' => ['id' => 'price_test123']],
                            ],
                        ],
                    ],
                ],
            ];

            $response = $this->controller->handleCustomerSubscriptionUpdated($payload);

            expect($response->getStatusCode())->toBe(200);
        });
    });

    describe('handleChargeRefunded', function () {
        it('logs charge refund', function () {
            $user = User::factory()->create([
                'stripe_id' => 'cus_testrefund',
            ]);

            $payload = [
                'data' => [
                    'object' => [
                        'customer' => 'cus_testrefund',
                        'amount_refunded' => 1500,
                    ],
                ],
            ];

            $response = $this->controller->handleChargeRefunded($payload);

            expect($response->getStatusCode())->toBe(200);
        });
    });
});
