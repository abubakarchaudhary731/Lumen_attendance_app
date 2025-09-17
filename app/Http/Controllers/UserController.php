<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\PermissionService;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\UsersRequest\UpdateUserRequest;

class UserController extends Controller
{

    protected $userService;
    protected $authService;
    protected $permissionService;

    public function __construct(
        UserService $userService,
        AuthService $authService,
        PermissionService $permissionService,
    ) {
        $this->userService = $userService;
        $this->authService = $authService;
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the resource with pagination and search.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            if (!$this->permissionService->haveAdminOrHRPermission()) {
                return ApiResponse::errorResponse(
                    __('messages.permissions.permission_denied'),
                    403
                );
            }

            // Get pagination and search parameters
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);
            $search = $request->input('search');

            // Get users with pagination and search
            $result = $this->userService->getAllUsers([
                'per_page' => $perPage,
                'page' => $page,
                'search' => $search
            ]);

            if (!$result['success']) {
                return ApiResponse::errorResponse(
                    $result['message'],
                    $result['code'] ?? 500
                );
            }

            return ApiResponse::successResponse(
                'Users fetched successfully',
                $result['data']
            );
        } catch (\Throwable $th) {
            return ApiResponse::errorResponse(
                $th->getMessage(),
                $th->getCode() ?: 500,
                $th
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterUserRequest $request)
    {
        try {
            if (!$this->permissionService->haveAdminOrHRPermission()) {
                return ApiResponse::errorResponse(
                    __('messages.permissions.permission_denied'),
                    403
                );
            }
            $validatedData = $request->validated();
            $response = $this->authService->register($validatedData);
            return ApiResponse::successResponse($response['message'], $response['user']);
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                $e->getMessage(),
                $e->getCode() ?: 500,
                $e
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $currentUser = auth()->user();

            // Check if current user is admin or HR, or if they're trying to access their own profile
            if (!$this->permissionService->haveAdminOrHRPermission() && $currentUser->id != $id) {
                return ApiResponse::errorResponse(
                    __('messages.permissions.permission_denied'),
                    403
                );
            }

            $requestedUser = $this->userService->getUserById($id);
            return ApiResponse::successResponse(
                'User fetched successfully',
                $requestedUser ? (array) $requestedUser : null
            );
        } catch (\Throwable $th) {
            return ApiResponse::errorResponse(
                $th->getMessage(),
                $th->getCode() ?: 500,
                $th
            );
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \App\Http\Requests\User\UpdateUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $targetUserId = (int)$id;
            $currentUser = auth()->user();

            if (!$currentUser) {
                return ApiResponse::errorResponse(
                    'Unauthenticated. Please log in to update user.',
                    401
                );
            }
            if (!$this->permissionService->haveAdminOrHRPermission() && $currentUser->id !== $targetUserId) {
                return ApiResponse::errorResponse(
                    __('messages.permissions.permission_denied'),
                    403
                );
            }

            $request->merge(['id' => $targetUserId]);
            $validatedRequest = $request->validated();

            $response = $this->userService->updateUser(
                $validatedRequest,
                $targetUserId,
                $currentUser
            );

            if (!$response['success']) {
                return ApiResponse::errorResponse(
                    $response['message'],
                    $response['code'] ?? 500,
                    $response['errors'] ?? null
                );
            }

            $responseData = $response['data'] ? (
                method_exists($response['data'], 'toArray')
                ? $response['data']->toArray()
                : (array)$response['data']
            ) : null;

            return ApiResponse::successResponse(
                $response['message'] ?? 'User updated successfully',
                $responseData
            );
        } catch (\Exception $e) {
            return ApiResponse::errorResponse(
                $e->getMessage(),
                $e->getCode() ?: 500,
                $e
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if ($this->permissionService->haveOnlyAdminPermission()) {
                $user = User::findOrFail($id);
                $user->delete();

                return ApiResponse::successResponse(
                    'User deleted successfully',
                    null
                );
            }
            return ApiResponse::errorResponse(
                __('messages.permissions.permission_denied'),
                403
            );
        } catch (\Throwable $th) {
            return ApiResponse::errorResponse(
                $th->getMessage(),
                $th->getCode() ?: 500,
                $th
            );
        }
    }
}
