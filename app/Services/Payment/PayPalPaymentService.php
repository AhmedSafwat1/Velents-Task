<?php

namespace App\Services\Payment;

use App\Enums\PaymentStatus;
use App\Enums\PaypalEvent;
use App\Events\PaymentEvent;
use App\Exceptions\PaymentException;
use App\Models\PaymentTransaction;
use App\Services\Contracts\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;

class PayPalPaymentService implements PaymentServiceInterface
{
    public GatewayInterface $gateway;

    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->initialize([
            'clientId' => config('payment.payments.paypal.clientId'),
            'secret' => config('payment.payments.paypal.secret'),
            'testMode' => config('payment.payments.paypal.testMode'),
        ]);
    }

    /**
     * Create purchase
     */
    public function purchase(PaymentTransaction $paymentTransaction): string
    {
        try {
            $response = $this->gateway->purchase([
                'amount' => $paymentTransaction->total, // Total amount to be charged
                'currency' => 'USD',
                'returnUrl' => route('payment.success'), // Redirect on success
                'cancelUrl' => route('payment.cancel', ['invoice_number' => $paymentTransaction->id]),  // Redirect on cancel
                'transactionId' => $paymentTransaction->id,
            ])->send();
            if ($response->isRedirect()) {
                // Redirect the user to PayPal
                return $response->getRedirectUrl();
            } else {
                // Payment failed
                $this->throwException($response->getMessage());
            }
        } catch (\Exception $e) {
            $this->throwException($e->getMessage());
        }

        return '';
    }

    /**
     * Webhook Handler for payment Service
     */
    public function webhookHandler(Request $request): void
    {
        // Get the PayPal webhook event
        $event = $request->all();

        // Log the event for debugging
        Log::info('Received PayPal webhook event:', $event);

        // Verify the webhook signature if needed
        // You can implement PayPal's verification here if necessary.

        $transactionId = Arr::get($event, 'resource.invoice_number');
        $eventType = PaypalEvent::tryFrom($event['event_type']);
        if ($transactionId && $eventType) {
            DB::transaction(function () use ($eventType, $transactionId, &$event) {
                // Handle different webhook events
                switch ($eventType) {
                    case PaypalEvent::COMPLETED:
                        $transaction = $this->findPaymentTransaction($transactionId);
                        if ($transaction) {
                            $this->success($transaction, $event['resource'] ?? []);
                        }
                        break;

                    case PaypalEvent::DENIED:
                    case PaypalEvent::REVERSED:
                    case PaypalEvent::CANCELLED:
                        $transaction = $this->findPaymentTransaction($transactionId);
                        if ($transaction) {
                            $this->failed($transaction, $event['resource'] ?? []);
                        }
                        break;

                    default:
                        Log::warning('Unhandled PayPal webhook event: '.$event['event_type']);
                        break;
                }
            });

        }
    }

    /**
     * Complete Purchase
     */
    public function completePurchase(Request $request): void
    {
        DB::transaction(function () use (&$request) {
            $parameters = [
                'payerId' => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ];
            try {
                $response = $this->gateway->completePurchase($parameters)->send();
                if ($response->isSuccessful()) {
                    $transaction = Arr::get($response->getData(), 'transactions.0');
                    $paymentTransaction = $this->findPaymentTransaction($transaction['invoice_number']);
                    abort_if(is_null($paymentTransaction), 404);
                    $this->success($paymentTransaction, $transaction);
                }
            } catch (\Exception $e) {
                $this->throwException($e->getMessage());
            }
        });

    }

    /**
     * Cancel Purchase
     */
    public function cancelPurchase(Request $request): void
    {
        DB::transaction(function () use (&$request) {
            $paymentTransaction = $this->findPaymentTransaction($request['invoice_number']);
            abort_if(is_null($paymentTransaction), 404);
            $this->failed($paymentTransaction, []);
        });
    }

    /**
     * Success
     */
    public function success(PaymentTransaction $paymentTransaction, array $transactionDetails = []): void
    {
        if ($paymentTransaction->status != PaymentStatus::PAID) {
            $paymentTransaction->update(['status' => PaymentStatus::PAID->value, 'payment_response' => $transactionDetails]);
            $handler = app()->make($paymentTransaction->handler);
            $handler->success($paymentTransaction);
            event(new PaymentEvent($paymentTransaction));
        }
    }

    /**
     * Failed
     */
    public function failed(PaymentTransaction $paymentTransaction, array $transactionDetails = []): void
    {
        if ($paymentTransaction->status != PaymentStatus::PAID) {
            $paymentTransaction->update(['status' => PaymentStatus::CANCELED->value, 'payment_response' => $transactionDetails]);
            $handler = app()->make($paymentTransaction->handler);
            $handler->failed($paymentTransaction);
            event(new PaymentEvent($paymentTransaction));
        }
    }

    /**
     * Throw Payment Exception
     *
     * @param  string  $message
     * @param  int  $statusCode
     *
     * @throws PaymentException
     */
    protected function throwException($message = 'Payment failed', $statusCode = 400): void
    {
        throw new PaymentException($message, $statusCode);
    }

    /**
     * Find Payment Transaction
     */
    protected function findPaymentTransaction(string $id): ?PaymentTransaction
    {
        return PaymentTransaction::where('id', $id)->lockForUpdate()->first();
    }
}
