<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\CancelRequest;
use App\Http\Requests\Payment\CompletePurchaseRequest;
use App\Services\Payment\PayPalPaymentService;

class PaypalPaymentController extends Controller
{
    public function __construct(
        /**
         * Payment Service
         *
         * @var PayPalPaymentService
         */
        protected PayPalPaymentService $payPalPaymentService
    ) {}

    /**
     * Complete Purchase
     */
    public function completePurchase(CompletePurchaseRequest $request): mixed
    {
        $this->payPalPaymentService->completePurchase($request);

        return 'success';
    }

    /**
     * Cancel Purchase
     */
    public function cancel(CancelRequest $request): mixed
    {
        $this->payPalPaymentService->cancelPurchase($request);

        return 'cancel';
    }
}
