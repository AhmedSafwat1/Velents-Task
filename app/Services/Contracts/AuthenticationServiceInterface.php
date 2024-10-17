<?php
namespace App\Services\Contracts;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Validation\ValidationException;

interface AuthenticationServiceInterface 
{
   /**
    * Set User name which use for login can be email or username
    *
    * @param string $username
    * @return self
    */
    public function setUserName(string $username):self;

    /**
    * Set Guard
    *
    * @param string $guard
    * @return self
    */
    public function setGuard(string $guard):self;

    /**
     * Set Request
     *
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request):self;


    /**
     * Authenticated
     *
     * @param array $credentials
     * @param boolean $remember
     * @return User
     * @throws ValidationException
     */
    public function authenticated(array $credentials, bool $remember = false):User;
}