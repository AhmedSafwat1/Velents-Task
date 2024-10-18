<?php

namespace App\Services;

use App\Factories\PaymentServiceFactory;
use App\Models\Order;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Payment\Handler\OrderPaymentServiceHandler;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        /**
         * Order
         *
         * @var Order
         */
        protected Order $orderModel
    ) {}

    /**
     * Create Order
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = $this->orderModel->create($data);
            $order->paymentTransactions()->create(['total' => $order->price, 'handler' => OrderPaymentServiceHandler::class]);
            $order->loadMissing('paymentTransactions');

            return $order;
        });
    }

    /**
     * Update
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
     * @return Collection
     */
    public function list(array $filters = [], array $with = [], bool $paginate = true): Collection|Paginator
    {
        $query = $this->orderModel->with($with)
            ->filter(
                $filters,
                [
                    'status' => \App\Support\Filter\Handler\Order\StatusFilter::class,
                    'from' => \App\Support\Filter\Handler\Order\FromFilter::class,
                    'to' => \App\Support\Filter\Handler\Order\ToFilter::class,
                ]
            );

        return $paginate ? $query->simplePaginate() : $query->get();
    }

    /**
     * Handle Payment
     *
     * @param  string  $method
     */
    public function handlePaymentUrl(Order $order, $method = 'paypal'): string
    {
        $paymentTransaction = $order->paymentTransactions;
        if ($paymentTransaction) {
            try {
                $paymentService = PaymentServiceFactory::create($method);

                return $paymentService->purchase($paymentTransaction);
            } catch (Exception $ex) {
                $order->delete();
                throw $ex;
            }
        }

        return '';
    }
}
