<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasUuids;

    protected $fillable = ['payment_response', 'handler', 'quantity', 'total', 'status'];

    protected $casts = [
        'status' => PaymentStatus::class, // Cast to enum
        'payment_response' => 'array',
    ];

    /**
     * Get the owning transactionable model (Order, Subscription, etc.).
     */
    public function transactionable()
    {
        return $this->morphTo();
    }
}
