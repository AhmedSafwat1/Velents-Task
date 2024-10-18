<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // Make sure to include the User model if you're using it

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $url = '/api/v1/auth/login';

    /**
     * Test successful login.
     *
     * @return void
     */
    public function test_successful_login()
    {
        // Create a user for testing
        $user = User::factory()->create([
            'password' => bcrypt('password123'), // Make sure to set the password correctly
        ]);

        // Attempt to log in with correct credentials
        $response = $this->postJson($this->url, [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // Assert that the response status is 200 and contains a token or user information
        $response->assertStatus(200);
    }

    /**
     * Test login with invalid credentials.
     *
     * @return void
     */
    public function test_login_with_invalid_credentials()
    {
        // Attempt to log in with invalid credentials
        $response = $this->postJson($this->url, [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert that the response status is 422(validation)
        $response->assertStatus(422);
    }

    /**
     * Test login validation.
     *
     * @return void
     */
    public function test_login_validation()
    {
        // Attempt to log in with empty credentials
        $response = $this->postJson($this->url, []);

        // Assert that the response status is 422 (Unprocessable Entity)
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'email',
                'password',
            ],
        ]);
    }
}
