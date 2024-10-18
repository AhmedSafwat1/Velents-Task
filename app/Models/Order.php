<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Support\Filter\Filterable;
use App\Traits\LogsOrderChanges;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Filterable;
    use LogsOrderChanges;

    protected $fillable = ['product_name', 'quantity', 'price', 'status'];

    protected $casts = [
        'status' => PaymentStatus::class, // Cast to enum
    ];

    /**
     * Get all of the order's payment transactions.
     */
    public function paymentTransactions()
    {
        return $this->morphOne(PaymentTransaction::class, 'transactionable');
    }
}
