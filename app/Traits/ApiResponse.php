<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Success response method for standard data.
     *
     * @param mixed $data
     * @param int $status
     * @param string|null $message
     * @return JsonResponse
     */
    protected function successResponse($data = [], int $status = 200, string $message = null): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Success response method for Laravel resources and resource collections.
     *
     * @param JsonResource|ResourceCollection $resource
     * @param int $status
     * @param string|null $message
     * @return JsonResponse
     */
    protected function successResourceResponse($resource, int $status = 200, string $message = null): JsonResponse
    {
        // Check if the resource is a Laravel resource collection
        if ($resource instanceof ResourceCollection) {
            $data = $resource->response()->getData(true);
        } elseif ($resource instanceof JsonResource) {
            // Convert a single resource to array
            $data = $resource->response()->getData(true);
        } else {
            // If it's not a resource, we can return an error or handle it as needed
            return $this->errorResponse('Invalid resource type', [], 400);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Error response method.
     *
     * @param string $message
     * @param array $errors
     * @param int $status
     * @return JsonResponse
     */
    protected function errorResponse(string $message = '', array $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
