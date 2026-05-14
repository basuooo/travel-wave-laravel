<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('webhooks')->group(function () {
    Route::post('/{platform}', [\App\Http\Controllers\API\WebhookController::class, 'handle'])
        ->where('platform', 'meta|tiktok');
    
    // Support for Meta's GET verification
    Route::get('/meta', [\App\Http\Controllers\API\WebhookController::class, 'handle'])
        ->defaults('platform', 'meta');

    // WhatsApp Cloud API Webhook
    Route::get('/whatsapp', [\App\Http\Controllers\Api\WhatsAppWebhookController::class, 'verify'])
        ->name('whatsapp.webhook.verify');
    Route::post('/whatsapp', [\App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle'])
        ->name('whatsapp.webhook.handle');
});
