<?php

namespace App\Services\Payment\Handler;

use App\Enums\PaymentStatus;
use App\Models\PaymentTransaction;
use App\Services\Contracts\PaymentServiceHandler;

class OrderPaymentServiceHandler implements PaymentServiceHandler
{
    /**
     * Success Payment
     */
    public function success(PaymentTransaction $paymentTransaction): void
    {
        if ($order = $paymentTransaction->transactionable) {
            $order->update(['status' => PaymentStatus::PAID->value]);
        }
    }

    /**
     * Failed Payment
     */
    public function failed(PaymentTransaction $paymentTransaction): void
    {
        if ($order = $paymentTransaction->transactionable) {
            $order->update(['status' => PaymentStatus::CANCELED->value]);
        }
    }
}
