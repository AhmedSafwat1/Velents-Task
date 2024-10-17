<?php

namespace App\Services\Contracts;

use App\Models\PaymentTransaction;

interface PaymentServiceHandler
{
    /**
     * Success Payment
     *
     * @param PaymentTransaction $paymentTransaction
     * @return void
     */
    public function success(PaymentTransaction $paymentTransaction): void;


    /**
     * Failed Payment
     *
     * @param PaymentTransaction $paymentTransaction
     * @return void
     */
    public function failed(PaymentTransaction $paymentTransaction): void;
}
