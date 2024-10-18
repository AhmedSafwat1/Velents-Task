<?php

namespace App\Services\Contracts;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

interface AuthenticationServiceInterface
{
    /**
     * Set User name which use for login can be email or username
     */
    public function setUserName(string $username): self;

    /**
     * Set Guard
     */
    public function setGuard(string $guard): self;

    /**
     * Set Request
     */
    public function setRequest(Request $request): self;

    /**
     * Authenticated
     *
     * @throws ValidationException
     */
    public function authenticated(array $credentials, bool $remember = false): ?User;
}
