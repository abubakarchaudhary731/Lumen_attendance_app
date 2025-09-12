<?php

namespace App\Response;

use Error;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\MessageBag;

class ApiResponse
{
    /**
     * Success response.
     *
     * @param string $message
     * @param array|\Illuminate\Http\Resources\Json\JsonResource $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function successResponse(string $message = '', array|JsonResource|JsonResponse $data = null): \Illuminate\Http\JsonResponse
    {
        $status = IlluminateResponse::HTTP_OK;

        if (is_null($data)) {
            return response()->json([
                'message'   => $message,
                'status'    => $status,
            ], $status);
        }

        if ($data instanceof JsonResponse) {
            $response = json_decode($data->content());
            if ($message != '') $response->message = $message;
            $response->status = $status;

            return response()->json($response, $status);
        }

        return response()->json([
            'message'   => $message,
            'data'      => $data,
            'status'    => $status,
        ], $status);
    }

    /**
     * Success response.
     *
     * @param array $data
     * @param array $headers
     * @return json response
     */
    public static function success($data = array(), $headers = array()): JsonResponse
    {
        $status = IlluminateResponse::HTTP_OK;
        $data['status'] = $status;
        return response()->json($data, $status, $headers);
    }

    /**
     * Success response.
     *
     * @param array $data
     * @param array $headers
     * @return json response
     */
    public static function successOutput(array|JsonResource $data = null, $headers = array()): JsonResponse
    {
        $status = IlluminateResponse::HTTP_OK;

        return response()->json($data, $status);
    }

    /**
     * Returns a JSON error response with status code and details.
     *
     * @param string              $message
     * @param int                 $statusCode
     * @param \Exception|\Error|null $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function errorResponse(
        string $message,
        int $statusCode = 500,
        Exception|Error|null $exception = null
    ): JsonResponse {
        return response()->json([
            "errors" => [
                "status_code"   => $statusCode,
                "message"       => $message,
                "error_details" => $exception
                    ? $exception->getMessage() . ' in file ' . $exception->getFile() . ' on line number ' . $exception->getLine()
                    : "",
            ]
        ], $statusCode);
    }


    /**
     * Validation fails.
     *
     * @param \Illuminate\Support\MessageBag $errors
     * @return json response
     */
    public static function validationErrorResponse(MessageBag $errors, int $statusCode = 422): JsonResponse
    {
        return response()->json([
            "errors" => $errors,
        ], $statusCode);
    }

    /**
     * Invalid fallback resquest.
     * @param string $message
     * @param int $status
     *
     * @return json response
     */
    public static function invalidFallback(string $message, int $status): JsonResponse
    {
        return response()->json([
            "errors" => [
                "status_code" => $status,
                "message"     => $message,
            ]
        ], $status);
    }
}
