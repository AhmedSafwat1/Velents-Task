<?php

namespace App\Http\Resources\Api\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id ,
            "product_name" => $this->product_name ,
            "quantity" => $this->quantity,
            "price" => $this->price,
            "status" => $this->status,
            "created_at" => $this->created_at->format("d-m-Y H:ia")
        ];
    }
}
