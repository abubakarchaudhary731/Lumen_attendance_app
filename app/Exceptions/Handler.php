<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use App\Response\ApiResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenExpiredException) {
            return ApiResponse::errorResponse(
                message: 'Token has expired',
                statusCode: 401,
                exception: $exception
            );
        }

        if ($exception instanceof TokenInvalidException) {
            return ApiResponse::errorResponse(
                message: 'Token is invalid',
                statusCode: 401,
                exception: $exception
            );
        }

        if ($exception instanceof JWTException) {
            return ApiResponse::errorResponse(
                message: 'Could not process token',
                statusCode: 500,
                exception: $exception
            );
        }

        // For other types of exceptions, use the default handler
        $response = parent::render($request, $exception);

        // If it's an API request, return JSON response using ApiResponse
        if ($request->wantsJson() || $request->is('api/*')) {
            $statusCode = method_exists($response, 'getStatusCode')
                ? $response->getStatusCode()
                : 500;

            return ApiResponse::errorResponse(
                message: $exception->getMessage() ?: 'An error occurred',
                statusCode: $statusCode,
                exception: $exception
            );
        }

        return $response;
    }
}
