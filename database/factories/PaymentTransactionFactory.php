<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\Payment\Handler\OrderPaymentServiceHandler;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $order = Order::factory()->create();
        return [
            "transactionable_type" => Order::class,
            "transactionable_id" => $order->id,
            "status"  => PaymentStatus::PENDING->value,
            "handler" => OrderPaymentServiceHandler::class,
            'total' => fake()->randomNumber(2),
        ];
    }
}
