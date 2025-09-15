<?php

namespace App\Http\Middleware;

use App\Response\ApiResponse;
use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            if (!Auth::check()) {
                return ApiResponse::errorResponse(
                    'Unauthorized. Please log in.',
                    401
                );
            }

            return $next($request);
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                'Token is invalid or expired.',
                401
            );
        }
    }
}
