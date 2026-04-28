<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        $secret = config('services.stripe.secret');
        if ($secret && $secret !== 'sk_test_dummy') {
            Stripe::setApiKey($secret);
        }
    }

    public function createPaymentIntent(int $userId, string $userEmail, int $amount = 500, string $currency = 'eur')
    {
        if (config('services.stripe.secret') === 'sk_test_dummy') {
            return (object) ['client_secret' => 'mock_secret', 'amount' => $amount, 'currency' => $currency, 'id' => 'mock_id'];
        }

        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => ['user_id' => $userId, 'user_email' => $userEmail, 'plan' => 'premium'],
        ]);
    }

    public function constructWebhookEvent(string $payload, string $signature, string $secret)
    {
        if ($secret === 'whsec_dummy') {
            return json_decode($payload);
        }
        return Webhook::constructEvent($payload, $signature, $secret);
    }
}