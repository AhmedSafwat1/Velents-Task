<?php

namespace App\Events;

use App\Models\PaymentTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    protected PaymentTransaction $paymentTransaction;

    /**
     * Create a new event instance.
     */
    public function __construct(PaymentTransaction $paymentTransaction)
    {
        $this->paymentTransaction = $paymentTransaction;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('payments'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'transactionable_type' => $this->paymentTransaction->transactionable_type,
            'transactionable_id' => $this->paymentTransaction->transactionable_id,
            'status' => $this->paymentTransaction->status,
            'amount' => $this->paymentTransaction->total,
        ];
    }
}
