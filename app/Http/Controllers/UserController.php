<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\UserService;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected $userService;
    protected $permissionService;

    public function __construct(UserService $userService, PermissionService $permissionService)
    {
        $this->userService = $userService;
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'sometimes|in:' . implode(',', UserRole::values()),
            'status' => 'sometimes|in:' . implode(',', \App\Enums\UserStatus::values()),
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'user' => User::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'sometimes|in:' . implode(',', UserRole::values()),
            'status' => 'sometimes|in:' . implode(',', \App\Enums\UserStatus::values()),
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }
}
