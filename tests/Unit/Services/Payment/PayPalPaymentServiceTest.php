<?php

namespace Tests\Unit\Services\Payment;

use App\Enums\PaymentStatus;
use App\Enums\PaypalEvent;
use App\Events\PaymentEvent;
use App\Exceptions\PaymentException;
use App\Models\PaymentTransaction;
use App\Services\Payment\PayPalPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mockery as m;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\ResponseInterface;
use Tests\TestCase;

class PayPalPaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PayPalPaymentService $paypalPaymentService;
    protected $gatewayMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the GatewayInterface
        $this->gatewayMock = m::mock(GatewayInterface::class);
        $this->paypalPaymentService =  m::mock(PayPalPaymentService::class)
                    ->shouldAllowMockingProtectedMethods()
                    ->makePartial();
        $this->paypalPaymentService->gateway = $this->gatewayMock;

        // Fake events with the correct facade
        Event::fake();
    }

    /**
    * Test can create a purchase
    *
    * @return void
    */
    public function test_it_can_create_a_purchase_and_return_redirect_url()
    {
        // Create a fake PaymentTransaction
        $paymentTransaction = PaymentTransaction::factory()->create([
            'total' => 100.00,
            'status' => PaymentStatus::PENDING,
        ]);

        // Mock the purchase response
        $responseMock = m::mock(ResponseInterface::class);
        $responseMock->shouldReceive('isRedirect')->andReturn(true);
        $responseMock->shouldReceive('getRedirectUrl')->andReturn('http://paypal.com/redirect');

        // Mock Request
        $requestMock = m::mock(\Omnipay\Common\Message\RequestInterface::class);

        $this->gatewayMock
            ->shouldReceive('purchase')
            ->with([
                'amount' => $paymentTransaction->total,
                'currency' => 'USD',
                'returnUrl' => route('payment.success'),
                'cancelUrl' => route('payment.cancel', ['invoice_number' => $paymentTransaction->id]),
                'transactionId' => $paymentTransaction->id,
            ])
            ->andReturn($requestMock);

        $requestMock
            ->shouldReceive('send')
            ->andReturn($responseMock);

        // Assert that purchase returns the correct redirect URL
        $redirectUrl = $this->paypalPaymentService->purchase($paymentTransaction);
        $this->assertEquals('http://paypal.com/redirect', $redirectUrl);
    }


    /**
     * Test it handles webhook event successfully
     *
     * @return void
     */
    public function test_it_handles_webhook_event_successfully()
    {
        // Prepare the request payload for a COMPLETED event
        $eventPayload = [
            'event_type' => PaypalEvent::COMPLETED->value,
            'resource' => [
                'invoice_number' => 'test_invoice_id',
            ],
        ];
        $request = Request::create('/', 'POST', $eventPayload);

        // Create a PaymentTransaction with status 'PENDING'
        $paymentTransaction = PaymentTransaction::factory()->create([
            'id' => 'test_invoice_id',
            'status' => PaymentStatus::PENDING->value,
        ]);

        // Mock the findPaymentTransaction method to return the created transaction
        $this->mockTransactionFind($paymentTransaction->id, $paymentTransaction);

        // Trigger the webhook handler
        $this->paypalPaymentService->webhookHandler($request);

        // Assert that the transaction status is updated to 'PAID'
        $this->assertEquals(PaymentStatus::PAID, $paymentTransaction->refresh()->status);

        // Assert that the PaymentEvent was dispatched
        Event::assertDispatched(PaymentEvent::class, function ($event) use ($paymentTransaction) {
            return $event->paymentTransaction->id === $paymentTransaction->id;
        });
    }

    /**
     * Test throw payment exception
     *
     * @return void
     */
    public function test_it_throws_payment_exception_when_purchase_fails()
    {
        // Create a fake PaymentTransaction
        $paymentTransaction = PaymentTransaction::factory()->create([
            'total' => 100.00,
            'status' => PaymentStatus::PENDING,
        ]);

        // Mock the purchase response to fail
        $responseMock = m::mock(ResponseInterface::class);
        $responseMock->shouldReceive('isRedirect')->andReturn(false);
        $responseMock->shouldReceive('getMessage')->andReturn('Payment failed');

        // Mock Request
        $requestMock = m::mock(\Omnipay\Common\Message\RequestInterface::class);


        $this->gatewayMock
            ->shouldReceive('purchase')
            ->andReturn($requestMock);

        $requestMock
            ->shouldReceive('send')
            ->andReturn($responseMock);

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Payment failed');

        // Call the purchase method and expect an exception
        $this->paypalPaymentService->purchase($paymentTransaction);
    }

    /**
     * Test can complete purchase successfully
     *
     * @return void
     */
    public function test_it_can_complete_purchase_successfully()
    {
        // Mock request data
        $request = Request::create('/', 'POST', [
            'PayerID' => 'payer123',
            'paymentId' => 'payment123',
        ]);

        // Mock the payment transaction and the response
        $paymentTransaction = PaymentTransaction::factory()->create([
            'id' => 'test_invoice_id',
            'status' => PaymentStatus::PENDING,
        ]);

        $responseMock = m::mock(ResponseInterface::class);
        $responseMock->shouldReceive('isSuccessful')->andReturn(true);
        $responseMock->shouldReceive('getData')->andReturn([
            'transactions' => [
                ['invoice_number' => 'test_invoice_id'],
            ],
        ]);

        // Mock Request
        $requestMock = m::mock(\Omnipay\Common\Message\RequestInterface::class);

        $this->gatewayMock->shouldReceive('completePurchase')
            ->with(['payerId' => 'payer123', 'transactionReference' => 'payment123'])
            ->andReturn($requestMock);

        $requestMock
            ->shouldReceive('send')
            ->andReturn($responseMock);

        $this->mockTransactionFind($paymentTransaction->id, $paymentTransaction);

        // Complete the purchase
        $this->paypalPaymentService->completePurchase($request);

        // Assert that the transaction status is updated
        $this->assertEquals(PaymentStatus::PAID, $paymentTransaction->refresh()->status);
        Event::assertDispatched(PaymentEvent::class);
    }

    /**
     * Helper method to mock the findPaymentTransaction method.
     */
    protected function mockTransactionFind(string $id, $transaction)
    {
        $this->paypalPaymentService->shouldReceive('findPaymentTransaction')->andReturn($transaction);
    }
}
