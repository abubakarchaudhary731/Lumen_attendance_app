<?php

namespace App\Http\Controllers;

use App\Response\ApiResponse;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Auth;
use App\Services\DailyStandupService;
use App\Http\Requests\DailyStandup\CreateDailyStandupRequest;

class DailyStandupController extends Controller
{
    protected $standupService;
    protected $permissionService;

    /**
     * Create a new DailyStandupController instance.
     *
     * @param DailyStandupService $standupService
     * @return void
     */
    public function __construct(
        DailyStandupService $standupService,
        PermissionService $permissionService
    ) {
        $this->standupService = $standupService;
        $this->permissionService = $permissionService;
    }

    public function create(CreateDailyStandupRequest $request)
    {
        try {
            if ($this->permissionService->haveOnlyAdminPermission()) {
                throw new \Exception(__('messages.permissions.permission_denied'), 403);
            }
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::user()->id;
            $result = $this->standupService->create($validatedData);
            return ApiResponse::successResponse("Standup created successfully", $result);
        } catch (\Throwable $th) {
            return ApiResponse::errorResponse($th->getMessage(), $th->getCode(), $th->getPrevious());
        }
    }
}
