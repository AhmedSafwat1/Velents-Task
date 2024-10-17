<?php

namespace App\Services\Payment\Handler;

use App\Models\PaymentTransaction;
use App\Services\Contracts\PaymentServiceHandler;

class OrderPaymentServiceHandler implements PaymentServiceHandler
{
    /**
    * Success Payment
    *
    * @param PaymentTransaction $paymentTransaction
    * @return void
    */
    public function success(PaymentTransaction $paymentTransaction): void
    {

    }


    /**
     * Failed Payment
     *
     * @param PaymentTransaction $paymentTransaction
     * @return void
     */
    public function failed(PaymentTransaction $paymentTransaction): void
    {

    }
}
