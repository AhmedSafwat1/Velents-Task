<?php

namespace App\Services\Contracts;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

interface OrderServiceInterface
{
    /**
     * Create Order
     */
    public function create(array $data): Order;

    /**
     * List
     *
     * @return Collection
     */
    public function list(array $filters = [], array $with = [], bool $paginate = true): Collection|Paginator;

    /**
     * Update
     */
    public function update(int $id, array $data): Order;

    /**
     * Handle Payment
     *
     * @param  string  $method
     */
    public function handlePaymentUrl(Order $order, $method = 'paypal'): string;
}
