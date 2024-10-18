<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Order\CreateOrderRequest;
use App\Http\Requests\Api\V1\Order\IndexOrderRequest;
use App\Http\Requests\Api\V1\Order\UpdateStatusOrderRequest;
use App\Http\Resources\Api\Order\OrderResource;
use App\Services\Contracts\OrderServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        /**
         * Authentication service
         *
         * @var OrderServiceInterface
         */
        protected OrderServiceInterface $orderService
    ) {}

    /**
     * List order
     */
    public function index(IndexOrderRequest $request): JsonResponse
    {
        return $this->successResourceResponse(
            OrderResource::collection($this->orderService->list($request->validated()))
        );
    }

    /**
     * Create new Order
     */
    public function create(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->validated());
        try {
            $url = $this->orderService->handlePaymentUrl($order, config('payment.default'));

            return $this->successResponse(
                [
                    'order' => new OrderResource($order),
                    'url' => $url,
                ]
            );
        } catch (Exception $ex) {
            return $this->errorResponse('Have Issue in Payment Plz  try again');
        }

    }

    /**
     * Update Order Status
     */
    public function updateStatus(UpdateStatusOrderRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->update($id, $request->validated());

        return $this->successResourceResponse(
            new OrderResource($order)
        );
    }
}
