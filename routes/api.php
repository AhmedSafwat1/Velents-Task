<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PayPalPaymentWebhookController;
use Illuminate\Support\Facades\Route;

// Route  V1
Route::group(['prefix' => 'v1'], function () {

    Route::middleware('throttle:global')->group(function ($route) {

        // Auth routes
        $route->group(['prefix' => 'auth'], function ($authRoute) {
            // Login
            $authRoute->post('/login', [AuthController::class, 'login']);
        });

    });

    Route::middleware(['throttle:auth', 'auth:api'])->group(function ($route) {
        $route->get('/me', function () {
            return response()->json(auth()->user());
        });

        // Orders Route
        $route->group(['prefix' => 'orders'], function ($orderRoute) {
            // Add
            $orderRoute->post('/', [OrderController::class, 'create'])
                ->middleware('permission:add_order');
            // List
            $orderRoute->get('/', [OrderController::class, 'index'])
                ->middleware('permission:show_order');
            // Change Status
            $orderRoute->patch('/{id}/status', [OrderController::class, 'updateStatus'])
                ->middleware('permission:change_status');
        });
    });

    Route::any('payment-webhook', PayPalPaymentWebhookController::class);

});
