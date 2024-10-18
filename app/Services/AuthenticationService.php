<?php

namespace App\Services;

use App\Services\Contracts\AuthenticationServiceInterface;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * The field name used for authentication (e.g., email or username).
     */
    private string $username = 'email';

    /**
     * The guard name which will use can be api or web.
     */
    private string $guard = 'api';

    /**
     * The current HTTP request instance, used for getting credentials and IP.
     */
    private ?Request $request = null;

    /**
     * Maximum number of allowed login attempts before the user is locked out.
     */
    private int $maxAttempts = 5;

    /**
     * Time in minutes before the user can try to login again after exceeding the max attempts.
     */
    private int $decayMinutes = 1;

    /**
     * Set the username field used for login, can be either email or username.
     */
    public function setUserName(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set Guard
     */
    public function setGuard(string $guard): self
    {
        $this->guard = $guard;

        return $this;
    }

    /**
     * Set the current request instance.
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Authenticate the user with the provided credentials.
     *
     * @throws ValidationException
     */
    public function authenticated(array $credentials, bool $remember = true): ?User
    {
        // Check if there are too many login attempts
        if ($this->hasTooManyLoginAttempts()) {
            $this->sendLockoutResponse();
        }

        // Attempt to authenticate the user
        if (Auth::guard($this->guard)->attempt($credentials, $remember)) {
            $this->clearLoginAttempts(); // Clear attempts on success

            return Auth::guard($this->guard)->user();
        }

        // Increment login attempts and throw invalid credentials exception on failure
        $this->incrementLoginAttempts();
        $this->sendInvalidCredentials();

        return null;
    }

    /**
     * Clear the login attempts for the given user credentials.
     */
    protected function clearLoginAttempts(): void
    {
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Determine if the user has too many failed login attempts.
     */
    protected function hasTooManyLoginAttempts(): bool
    {
        return RateLimiter::tooManyAttempts(
            $this->throttleKey(),
            $this->maxAttempts
        );
    }

    /**
     * Increment the login attempts for the user.
     */
    protected function incrementLoginAttempts(): void
    {
        RateLimiter::hit(
            $this->throttleKey(),
            $this->decayMinutes * 60
        );
    }

    /**
     * Send the lockout response when the user has too many failed login attempts.
     *
     * @throws ValidationException
     */
    protected function sendLockoutResponse(): void
    {
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            $this->username => [trans('auth.throttle', ['seconds' => $seconds])],
        ])->status(429);
    }

    /**
     * Send validation error for invalid credentials.
     *
     * @throws ValidationException
     */
    protected function sendInvalidCredentials(): void
    {
        throw ValidationException::withMessages([
            $this->username => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the rate limiting throttle key for the current request.
     */
    protected function throttleKey(): string
    {
        return strtolower($this->request->input($this->username)).'|'.$this->request->ip();
    }
}
