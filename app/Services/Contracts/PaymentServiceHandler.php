<?php

namespace App\Services\Contracts;

use App\Models\PaymentTransaction;

interface PaymentServiceHandler
{
    /**
     * Success Payment
     */
    public function success(PaymentTransaction $paymentTransaction): void;

    /**
     * Failed Payment
     */
    public function failed(PaymentTransaction $paymentTransaction): void;
}
