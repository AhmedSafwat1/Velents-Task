<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Services\Contracts\AuthenticationServiceInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * The field name used for authentication (e.g., email or username).
     *
     * @var string
     */
    private string $username = "email";

    /**
     * The guard name which will use can be api or web.
     *
     * @var string
     */
    private string $guard = "api";

    /**
     * The current HTTP request instance, used for getting credentials and IP.
     *
     * @var Request|null
     */
    private ?Request $request = null;

    /**
     * Maximum number of allowed login attempts before the user is locked out.
     *
     * @var int
     */
    private int $maxAttempts = 5;

    /**
     * Time in minutes before the user can try to login again after exceeding the max attempts.
     *
     * @var int
     */
    private int $decayMinutes = 1;

    /**
     * Set the username field used for login, can be either email or username.
     *
     * @param string $username
     * @return self
     */
    public function setUserName(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
    * Set Guard
    *
    * @param string $guard
    * @return self
    */
    public function setGuard(string $guard): self
    {
        $this->guard = $guard;
        return $this;
    }

    /**
     * Set the current request instance.
     *
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Authenticate the user with the provided credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return User
     * @throws ValidationException
     */
    public function authenticated(array $credentials, bool $remember = true): User
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
    }

    /**
     * Clear the login attempts for the given user credentials.
     *
     * @return void
     */
    protected function clearLoginAttempts(): void
    {
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @return bool
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
     *
     * @return void
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
     * @return void
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
     * @return void
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
     *
     * @return string
     */
    protected function throttleKey(): string
    {
        return strtolower($this->request->input($this->username)) . '|' . $this->request->ip();
    }
}
