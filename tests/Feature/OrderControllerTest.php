<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // Include the Order model if you have one

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $url = '/api/v1/orders';

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        // Create a user for testing
        $this->user = User::factory()->create([
            'password' => bcrypt('password123'), // Set a password for the user
        ]);

        // Grant permissions to the user
        $this->user->givePermissionTo(['add_order', 'show_order', 'change_status']);
    }

    /**
     * Test creating a new order.
     *
     * @return void
     */
    public function test_create_order()
    {
        // Mock the payment service to avoid calling external payment gateways
        $orderServiceMock = $this->mock(OrderService::class);
        $orderServiceMock->shouldReceive('handlePaymentUrl')
            ->once()
            ->andReturn('https://payment-gateway.test/payment-url');

        // Authenticate the user
        $token = auth('api')->login($this->user);

        // Create a sample order
        $order = Order::factory()->create([
            'product_name' => 'Test Item',
            'quantity' => 5,
            'price' => 100.00,
        ]);

        $orderServiceMock->shouldReceive('create')
            ->once()
            ->andReturn($order);

        // Attempt to create an order
        $response = $this->postJson($this->url, [
            'product_name' => 'Test Item',
            'quantity' => 5,
            'price' => 100.00,
        ], [
            'Authorization' => 'Bearer '.$token,
        ]);

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the order is created with correct data
        $this->assertDatabaseHas('orders', [
            'product_name' => 'Test Item',
            'quantity' => 5,
            'price' => 100.00,
        ]);

        // Assert that the response contains the payment URL
        $response->assertJsonStructure([
            'data' => [
                'order' => [
                    'id',
                    'product_name',
                    'quantity',
                    'price',
                ],
                'url',
            ],
        ]);

        // Verify that the returned payment URL is correct
        $this->assertEquals('https://payment-gateway.test/payment-url', $response->json('data.url'));
    }

    /**
     * Test listing orders.
     *
     * @return void
     */
    public function test_list_orders()
    {
        // Authenticate the user
        $token = auth('api')->login($this->user);

        // Create a sample order
        Order::factory()->create([
            'product_name' => 'Sample Item',
            'quantity' => 10,
            'price' => 50.00,
        ]);

        // Attempt to list orders
        $response = $this->getJson($this->url, [
            'Authorization' => 'Bearer '.$token,
        ]);

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the response contains the created order
        $response->assertJsonFragment(['product_name' => 'Sample Item']);
    }

    /**
     * Test changing the status of an order.
     *
     * @return void
     */
    public function test_change_order_status()
    {
        // Authenticate the user
        $token = auth('api')->login($this->user);

        // Create a sample order
        $order = Order::factory()->create([
            'product_name' => 'Sample Item',
        ]);

        // Attempt to change the order status
        $response = $this->patchJson($this->url.'/'.$order->id.'/status', [
            'status' => PaymentStatus::PAID->value,
        ], [
            'Authorization' => 'Bearer '.$token,
        ]);

        // Assert that the response status is 200
        $response->assertStatus(200);

        // Assert that the order status is updated in the database
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => PaymentStatus::PAID->value,
        ]);
    }

    /**
     * Test unauthorized user trying to create an order.
     *
     * @return void
     */
    public function test_unauthorized_user_create_order()
    {
        // Attempt to create an order without authentication
        $response = $this->postJson($this->url, [
            'product_name' => 'Test Item',
            'quantity' => 5,
            'price' => 100.00,
        ]);

        // Assert that the response status is 401 (Unauthorized)
        $response->assertStatus(401);
    }

    /**
     * Test user without permission trying to create an order.
     *
     * @return void
     */
    public function test_user_without_permission_create_order()
    {
        // Create a user without the add_order permission
        $userWithoutPermission = User::factory()->create([
            'password' => bcrypt('password123'), // Set a password for the user
        ]);

        // Authenticate the user
        $token = auth('api')->login($userWithoutPermission);

        // Attempt to create an order
        $response = $this->postJson($this->url, [
            'product_name' => 'Test Item',
            'quantity' => 5,
            'price' => 100.00,
        ], [
            'Authorization' => 'Bearer '.$token,
        ]);

        // Assert that the response status is 403 (Forbidden)
        $response->assertStatus(403);
    }
}
