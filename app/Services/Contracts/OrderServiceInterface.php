<?php

namespace App\Services\Contracts;

use App\Models\Order;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderServiceInterface
{
    /**
     * Create Order
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order;

    /**
     * List
     *
     * @param array $filters
     * @param array $with
     * @param boolean $paginate
     * @return Collection
     */
    public function list(array $filters = [], array $with = [], bool $paginate = true): Collection|Paginator;


    /**
    * Update
    *
    * @param integer $id
    * @param array $data
    * @return Order
    */
    public function update(int $id, array $data): Order;
}
