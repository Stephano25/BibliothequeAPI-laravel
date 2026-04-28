<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\BorrowController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\WebhookController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::prefix('v1')->group(function () {
    Route::get('/books/{id}/summary', [BookController::class, 'getSummary']);
    Route::post('/books/smart-search', [BookController::class, 'smartSearch']);
    Route::post('/webhooks/stripe', [WebhookController::class, 'handleStripeWebhook']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/books/{id}/summary', [BookController::class, 'generateSummary']);
        Route::post('/subscriptions/payment-intent', [SubscriptionController::class, 'createPaymentIntent']);
        Route::get('/subscriptions/status', [SubscriptionController::class, 'getStatus']);
        Route::middleware('check.borrow.limit')->post('/borrows', [BorrowController::class, 'store']);
        Route::post('/borrows/{id}/return', [BorrowController::class, 'return']);
    });
});