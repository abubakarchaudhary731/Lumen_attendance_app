<?php

namespace App\Http\Controllers;

use App\Response\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    protected $authService;

    /**
     * Create a new AuthController instance.
     *
     * @param AuthService $authService
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $response = $this->authService->register($validatedData);
            return ApiResponse::successResponse($response['message'], $response['user']);
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginUserRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $response = $this->authService->login($validatedData);
            return ApiResponse::successResponse("User Logged In Successfully", $response);
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            // Get the token from the request
            $token = JWTAuth::getToken();

            if (!$token) {
                return ApiResponse::errorResponse(
                    'Token not provided',
                    401
                );
            }

            // Check if token is valid
            try {
                JWTAuth::checkOrFail();
            } catch (TokenExpiredException $e) {
                // Token is expired, we'll still allow refresh
            } catch (JWTException $e) {
                return ApiResponse::errorResponse(
                    'Invalid token: ' . $e->getMessage(),
                    401
                );
            }

            // Generate new token
            $newToken = JWTAuth::refresh($token);

            // Invalidate old token
            JWTAuth::invalidate($token);

            $response = $this->authService->respondWithToken($newToken);
            return ApiResponse::successResponse("Token Refreshed Successfully", $response);
        } catch (TokenExpiredException $e) {
            return ApiResponse::errorResponse(
                'Token has expired and can no longer be refreshed',
                401
            );
        } catch (TokenInvalidException $e) {
            return ApiResponse::errorResponse(
                'Token is invalid',
                401
            );
        } catch (JWTException $e) {
            return ApiResponse::errorResponse(
                'Could not refresh token: ' . $e->getMessage(),
                401
            );
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                'An error occurred during token refresh: ' . $e->getMessage(),
                500
            );
        }
    }
}
