<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\AttendanceService;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Attendance\CheckinRequest;
use App\Http\Requests\Attendance\CheckoutRequest;
use App\DTO\Attendance\PaginatedAttendanceResponseDTO;

class AttendanceController extends Controller
{
    protected $attendanceService;
    protected $permissionService;

    public function __construct(
        AttendanceService $attendanceService,
        PermissionService $permissionService,
    ) {
        $this->attendanceService = $attendanceService;
        $this->permissionService = $permissionService;
    }
    /**
     * Check in a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkIn(CheckinRequest $request)
    {
        try {
            $validatedData = $request->validated();
            if ($this->permissionService->haveOnlyAdminPermission()) {
                return ApiResponse::errorResponse(
                    'Unauthorized',
                    403,
                    null
                );
            }
            $response = $this->attendanceService->checkIn(Auth::user(), $validatedData);

            if (!$response['success']) {
                return ApiResponse::errorResponse(
                    $response['message'],
                    $response['code'] ?? 500,
                    null
                );
            }

            return ApiResponse::successResponse(
                $response['message'],
                $response['data']
            );
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * Check out a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkOut(CheckoutRequest $request)
    {
        try {
            $validatedData = $request->validated();
            if ($this->permissionService->haveOnlyAdminPermission()) {
                return ApiResponse::errorResponse(
                    'Unauthorized',
                    403,
                    null
                );
            }

            $response = $this->attendanceService->checkOut(Auth::user(), $validatedData);

            if (!$response['success']) {
                return ApiResponse::errorResponse(
                    $response['message'],
                    $response['code'] ?? 500,
                    null
                );
            }

            return ApiResponse::successResponse(
                $response['message'],
                $response['data']
            );
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                $e->getMessage(),
                500,
                $e
            );
        }
    }

    /**
     * Display a listing of the attendance records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $params = $request->all();

            if (!isset($params['per_page'])) {
                $params['per_page'] = 15;
            }

            $result = $this->attendanceService->listAttendances($user, $params);

            if (!$result['success']) {
                return ApiResponse::errorResponse(
                    $result['message'],
                    500,
                    null
                );
            }

            $responseDTO = PaginatedAttendanceResponseDTO::fromPaginator($result['data']);

            return ApiResponse::successResponse(
                'Attendance records retrieved successfully',
                $responseDTO->toArray()
            );
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                'Failed to retrieve attendance records: ' . $e->getMessage(),
                500,
                null
            );
        }
    }

    /**
     * Display the specified attendance record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        $user = Auth::user();

        // Only allow admin or the user who owns the attendance record to view it
        if ($user->role !== 'ADMIN' && $attendance->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'attendance' => $attendance
        ]);
    }
}
