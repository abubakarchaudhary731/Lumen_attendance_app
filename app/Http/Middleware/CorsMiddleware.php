<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin' => env('CORS_ALLOWED_ORIGINS', '*'),
            'Access-Control-Allow-Methods' => env('CORS_ALLOWED_METHODS', 'GET, POST, PUT, PATCH, DELETE, OPTIONS'),
            'Access-Control-Allow-Headers' => env('CORS_ALLOWED_HEADERS', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept'),
            'Access-Control-Expose-Headers' => env('CORS_EXPOSED_HEADERS', 'Authorization'),
            'Access-Control-Max-Age' => env('CORS_MAX_AGE', '0'),
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->json('OK', 200, $headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}
