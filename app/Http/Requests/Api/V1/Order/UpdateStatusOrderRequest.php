<?php

namespace App\Http\Requests\Api\V1\Order;

use App\Enums\PaymentStatus;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           "status" => ["sometimes",  new Enum(PaymentStatus::class)] ,
        ];
    }
}
