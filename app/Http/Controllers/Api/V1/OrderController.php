<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Order\OrderResource;
use App\Services\Contracts\OrderServiceInterface;
use App\Http\Requests\Api\V1\Order\IndexOrderRequest;
use App\Http\Requests\Api\V1\Order\CreateOrderRequest;
use App\Http\Requests\Api\V1\Order\UpdateStatusOrderRequest;

class OrderController extends Controller
{
    public function __construct(
        /**
         * Authentication service
         *
         * @var OrderServiceInterface
         */
        protected OrderServiceInterface $orderService
    ) {
    }

    /**
     * List order
     *
     * @param IndexOrderRequest $request
     * @return JsonResponse
     */
    public function index(IndexOrderRequest $request): JsonResponse
    {
        return $this->successResourceResponse(
            OrderResource::collection($this->orderService->list($request->validated()))
        );
    }

    /**
     * Create new Order
     *
     * @param CreateOrderRequest $request
     * @return JsonResponse
     */
    public function create(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->validated());
        return $this->successResourceResponse(
            new OrderResource($order)
        );
    }

    /**
     * Update Order Status
     *
     * @param UpdateStatusOrderRequest $request
     * @param integer $id
     * @return JsonResponse
     */
    public function updateStatus(UpdateStatusOrderRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->update($id, $request->validated());
        return $this->successResourceResponse(
            new OrderResource($order)
        );
    }
}
