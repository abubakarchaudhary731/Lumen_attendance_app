<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\AttendanceService;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Attendance\CheckinRequest;
use App\Http\Requests\Attendance\CheckoutRequest;
use App\DTO\Attendance\PaginatedAttendanceResponseDTO;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;

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
                throw new \Exception(__('messages.permissions.permission_denied'), 403);
            }
            $response = $this->attendanceService->checkIn(Auth::user(), $validatedData);

            return ApiResponse::successResponse(
                $response['message'],
                $response['data']
            );
        } catch (\Throwable $th) {
            return ApiResponse::errorResponse(
                $th->getMessage(),
                $th->getCode() ?? 500,
                $th
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
                throw new \Exception(__('messages.permissions.permission_denied'), 403);
            }

            $response = $this->attendanceService->checkOut(Auth::user(), $validatedData);

            return ApiResponse::successResponse(
                $response['message'],
                $response['data']
            );
        } catch (\Throwable $th) {
            return ApiResponse::errorResponse(
                $th->getMessage(),
                $th->getCode() ?? 500,
                $th
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

            $responseDTO = PaginatedAttendanceResponseDTO::fromPaginator($result['data']);

            return ApiResponse::successResponse(
                'Attendance records retrieved successfully',
                $responseDTO->toArray()
            );
        } catch (\Throwable $th) {
            return ApiResponse::errorResponse(
                $th->getMessage(),
                $th->getCode() ?? 500,
                $th
            );
        }
    }

    /**
     * Update the specified attendance record.
     *
     * @param  \App\Http\Requests\UpdateAttendanceRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttendanceRequest $request, $id)
    {
        try {
            if (!$this->permissionService->haveAdminOrHRPermission()) {
                throw new \Exception(__('messages.permissions.permission_denied'), 403);
            }
            $validatedData = $request->validated();

            $result = $this->attendanceService->updateAttendance(
                $id,
                $validatedData
            );

            return ApiResponse::successResponse(
                $result['message'],
                $result['data']
            );
        } catch (\Throwable $th) {
            return ApiResponse::errorResponse(
                $th->getMessage(),
                $th->getCode() ?? 500,
                $th
            );
        }
    }
}
