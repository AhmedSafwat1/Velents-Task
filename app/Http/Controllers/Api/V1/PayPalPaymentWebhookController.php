<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Payment\PayPalPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayPalPaymentWebhookController extends Controller
{
    public function __construct(
        /**
         * Payment Service
         *
         * @var PayPalPaymentService
         */
        protected PayPalPaymentService $payPalPaymentService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        // return $this->successResponse();

        $json = '{"id":"WH-0T490472X6099635W-7LJ29748BW389372K","event_version":"1.0","create_time":"2015-09-25T23:14:14Z","resource_type":"invoices","event_type":"INVOICING.INVOICE.PAID","summary":"An invoice was created","resource":{"id":"INV2-8FSD-3HT6-BRHR-UHYV","number":"MM00063","status":"PAID","merchant_info":{"email":"example@outlook.com","first_name":"Dennis","last_name":"Doctor","business_name":"Medical Professional LLC","address":{"line1":"1234 Main St","line2":"Apt 302","city":"Portland","state":"OR","postal_code":"97217","country_code":"US"}},"billing_info":[{"email":"example@example.com","business_name":"Medical Professionals LLC","language":"en_US"}],"items":[{"name":"Sample Item","quantity":1,"unit_price":{"currency":"USD","value":"1.00"},"unit_of_measure":"QUANTITY"}],"invoice_date":"2015-09-28 PDT","payment_term":{"term_type":"DUE_ON_RECEIPT","due_date":"2015-09-28 PDT"},"tax_calculated_after_discount":true,"tax_inclusive":false,"total_amount":{"currency":"USD","value":"1.00"},"payments":[{"type":"PAYPAL","transaction_id":"22592127VV907111U","transaction_type":"SALE","method":"PAYPAL","date":"2015-09-28 14:37:13 PDT"}],"metadata":{"created_date":"2015-09-28 14:35:46 PDT","last_updated_date":"2015-09-28 14:37:13 PDT","first_sent_date":"2015-09-28 14:35:47 PDT","last_sent_date":"2015-09-28 14:35:47 PDT"},"paid_amount":{"paypal":{"currency":"USD","value":"1.00"}},"links":[{"rel":"self","href":"https://api.paypal.com/v1/invoicing/invoices/INV2-8FSD-3HT6-BRHR-UHYV","method":"GET"}]},"links":[{"href":"https://api.paypal.com/v1/notifications/webhooks-events/WH-0T490472X6099635W-7LJ29748BW389372K","rel":"self","method":"GET"},{"href":"https://api.paypal.com/v1/notifications/webhooks-events/WH-0T490472X6099635W-7LJ29748BW389372K/resend","rel":"resend","method":"POST"}]} 
';
        $x = (json_decode($json, true));
        $x['resource']['invoice_number'] = '9d44c32c-2b94-4257-87eb-7721d6d1f0dd';
        $x['event_type'] = 'PAYMENT.SALE.COMPLETED';
        $this->payPalPaymentService->webhookHandler($request->merge($x));

        return $this->successResponse();
    }
}
