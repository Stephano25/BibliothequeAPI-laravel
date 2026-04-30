<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\BorrowController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\WebhookController;

// Routes d'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Routes API V1
Route::prefix('v1')->group(function () {
    // Routes publiques
    Route::get('/books/{id}/summary', [BookController::class, 'getSummary']);
    Route::post('/books/smart-search', [BookController::class, 'smartSearch']);
    Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripeWebhook']);
    
    // Routes protégées
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/books/{id}/summary', [BookController::class, 'generateSummary']);
        Route::get('/books/{id}/summary/stream', [BookController::class, 'generateSummaryStream']);
        Route::post('/subscriptions/payment-intent', [SubscriptionController::class, 'createPaymentIntent']);
        Route::get('/subscriptions/status', [SubscriptionController::class, 'getStatus']);
        Route::post('/borrows', [BorrowController::class, 'store']);
        Route::post('/borrows/{id}/return', [BorrowController::class, 'return']);
    });
});
