<?php

namespace App\Services\Contracts;

use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

interface PaymentServiceInterface
{
    /**
     * Create purchase
     */
    public function purchase(PaymentTransaction $paymentTransaction): string;

    /**
     * Webhook Handler for payment Service
     */
    public function webhookHandler(Request $request): void;

    /**
     * Complete Purchase
     */
    public function completePurchase(Request $request): void;

    /**
     * Cancel Purchase
     */
    public function cancelPurchase(Request $request): void;

    /**
     * Success
     */
    public function success(PaymentTransaction $paymentTransaction, array $transactionDetails = []): void;

    /**
     * Failed
     */
    public function failed(PaymentTransaction $paymentTransaction, array $transactionDetails = []): void;
}
