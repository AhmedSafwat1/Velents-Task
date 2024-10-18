<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Services\Contracts\AuthenticationServiceInterface;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        /**
         * Authentication service
         *
         * @var AuthenticationServiceInterface
         */
        protected AuthenticationServiceInterface $authenticationService
    ) {}

    /**
     * Login Request
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authenticationService
            ->setUserName('email')
            ->setRequest($request)
            ->authenticated($request->validated());

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $this->prepareTokenResponse(),
        ]);
    }

    /**
     * Get the token array structure.
     */
    protected function prepareTokenResponse(): array
    {
        return [
            'access_token' => auth('api')->getToken()?->get(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}
