<?php

namespace App\Factories;

use App\Services\Contracts\PaymentServiceInterface;
use App\Services\Payment\PayPalPaymentService;

class PaymentServiceFactory
{
    public static function create(string $method): PaymentServiceInterface
    {
        switch ($method) {
            case 'paypal':
                return new PayPalPaymentService;
            default:
                throw new \InvalidArgumentException("Invalid payment method: $method");
        }
    }
}
