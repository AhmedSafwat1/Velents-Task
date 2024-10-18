<?php

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('payments.{transactionId}', function ($user, $transactionId) {
    $transaction = PaymentTransaction::with('transactionable')->find($transactionId);
    if ($transaction->transactionable instanceof Order) {
        return $user->can('show_order');
    }
});
