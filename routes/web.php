<?php

use App\Http\Controllers\PaypalPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(["prefix" => "payments","middleware" => "throttle:payment"], function ($paymentRoute) {
    $paymentRoute->get("/success", [PaypalPaymentController::class,"completePurchase"])->name("payment.success");
    $paymentRoute->get("/cancel", [PaypalPaymentController::class,"cancel"])->name("payment.cancel");
});
