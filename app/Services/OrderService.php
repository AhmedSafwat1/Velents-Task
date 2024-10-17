<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Payment\Handler\OrderPaymentServiceHandler;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        /**
         * Order
         *
         * @var Order
         */
        protected Order $orderModel
    ) {
    }

    /**
     * Create Order
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = $this->orderModel->create($data);
            $order->paymentTransactions()->create(["total" => $order->price ,"handler" => OrderPaymentServiceHandler::class]);
            $order->loadMissing("paymentTransactions");
            return $order;
        });
    }

    /**
     * Update
     *
     * @param integer $id
     * @param array $data
     * @return Order
     */
    public function update(int $id, array $data): Order
    {
        $model = $this->orderModel->findOrFail($id);
        return DB::transaction(function () use ($data, $model) {
            $model->update($data);
            return $model;
        });
    }

    /**
     * List
     *
     * @param array $filters
     * @param array $with
     * @param boolean $paginate
     * @return Collection
     */
    public function list(array $filters = [], array $with = [], bool $paginate = true): Collection|Paginator
    {
        $query = $this->orderModel->with($with)
                    ->filter(
                        $filters,
                        [
                            "status" => \App\Support\Filter\Handler\Order\StatusFilter::class ,
                            "from"   => \App\Support\Filter\Handler\Order\FromFilter::class ,
                            "to"     => \App\Support\Filter\Handler\Order\ToFilter::class
                        ]
                    )
        ;
        return $paginate ? $query->simplePaginate() : $query->get();
    }
}
