<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Response\ApiResponse;
use App\Services\AttendanceService;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Attendance\CheckinRequest;
use App\Http\Requests\Attendance\CheckoutRequest;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $query = Attendance::with('user');

        // If user is not admin, only show their own attendance
        if ($user->role !== 'ADMIN') {
            $query->where('user_id', $user->id);
        }

        $attendance = $query->orderBy('check_in', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'attendance' => $attendance
        ]);
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
