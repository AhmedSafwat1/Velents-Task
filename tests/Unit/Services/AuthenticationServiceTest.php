<?php

namespace Tests\Unit\Services;

use App\Services\AuthenticationService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthenticationServiceTest extends TestCase
{
    protected AuthenticationService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthenticationService();
        $this->authService->setRequest(new Request());
    }

    /**
     * Test that a user is successfully authenticated when valid credentials are provided.
     */
    public function testAuthenticatedSuccess()
    {
        // Mock user credentials
        $credentials = ['email' => 'test@example.com', 'password' => 'password'];

        // Mock Auth facade
        Auth::shouldReceive('attempt')->with($credentials, false)->andReturn(true);
        Auth::shouldReceive('user')->andReturn(new User());

        // Mock RateLimiter facade
        RateLimiter::shouldReceive('clear')->once();
        RateLimiter::shouldReceive('tooManyAttempts')->andReturn(false);

        $user = $this->authService->authenticated($credentials);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test that a ValidationException is thrown when there are too many login attempts.
     */
    public function testAuthenticatedTooManyAttempts()
    {
        $this->authService->setRequest(new Request());

        RateLimiter::shouldReceive('tooManyAttempts')->andReturn(true);
        RateLimiter::shouldReceive('availableIn')->andReturn(30);

        $this->expectException(ValidationException::class);
        $this->authService->authenticated(['email' => 'test@example.com', 'password' => 'password']);
    }

    /**
     * Test that a ValidationException is thrown when invalid credentials are provided.
     */
    public function testAuthenticatedInvalidCredentials()
    {
        // Mock user credentials
        $credentials = ['email' => 'test@example.com', 'password' => 'wrongpassword'];

        // Mock Auth facade
        Auth::shouldReceive('attempt')->with($credentials, false)->andReturn(false);

        // Mock RateLimiter facade
        RateLimiter::shouldReceive('tooManyAttempts')->andReturn(false);
        RateLimiter::shouldReceive('hit')->once();

        $this->expectException(ValidationException::class);
        $this->authService->authenticated($credentials);
    }
}
