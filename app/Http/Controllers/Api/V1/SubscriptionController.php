<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function createPaymentIntent(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }

        if ($user->subscription_status === 'premium') {
            return response()->json(['success' => false, 'message' => 'Déjà premium'], 400);
        }

        try {
            $paymentIntent = $this->stripeService->createPaymentIntent($user->id, $user->email);
            return response()->json(['success' => true, 'data' => [
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
            ]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getStatus(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json(['success' => true, 'data' => [
            'subscription_status' => $user->subscription_status,
            'active_borrows' => $user->activeBorrowsCount(),
        ]]);
    }
}