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

// Public 1-Click Zapier Catch Endpoint (No Auth Needed)
Route::post('v1/zapier/incoming-lead', [\App\Http\Controllers\API\ZapierController::class, 'incomingLead']);

/*
|--------------------------------------------------------------------------
| Zapier Integration Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/zapier')->middleware('auth:sanctum')->group(function () {
    // Auth Verification
    Route::get('/me', [\App\Http\Controllers\API\ZapierController::class, 'me']);

    // REST Hooks Subscriptions
    Route::post('/subscribe', [\App\Http\Controllers\API\ZapierSubscriptionController::class, 'subscribe']);
    Route::delete('/unsubscribe', [\App\Http\Controllers\API\ZapierSubscriptionController::class, 'unsubscribe']);
    Route::get('/subscriptions', [\App\Http\Controllers\API\ZapierSubscriptionController::class, 'index']);

    // Triggers & Actions: Customers
    Route::get('/customers', [\App\Http\Controllers\API\ZapierController::class, 'listCustomers']);
    Route::post('/customers', [\App\Http\Controllers\API\ZapierController::class, 'createCustomer']);

    // Triggers & Actions: Inquiries
    Route::get('/inquiries', [\App\Http\Controllers\API\ZapierController::class, 'listInquiries']);
    Route::post('/inquiries', [\App\Http\Controllers\API\ZapierController::class, 'createInquiry']);

    // Triggers & Actions: Tasks
    Route::get('/tasks', [\App\Http\Controllers\API\ZapierController::class, 'listTasks']);
    Route::post('/tasks', [\App\Http\Controllers\API\ZapierController::class, 'createTask']);
});


