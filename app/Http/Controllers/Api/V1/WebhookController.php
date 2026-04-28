<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function handleStripeWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = $this->stripeService->constructWebhookEvent($payload, $signature, config('services.stripe.webhook_secret'));
            
            if ($event->type === 'payment_intent.succeeded') {
                $userId = $event->data->object->metadata->user_id ?? null;
                if ($userId && $user = User::find($userId)) {
                    $user->subscription_status = 'premium';
                    $user->save();
                    Log::info("User {$userId} upgraded to premium");
                }
            }
            return response()->json(['received' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Signature invalide'], 401);
        }
    }
}