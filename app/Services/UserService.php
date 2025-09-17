<?php

namespace App\Services;

use App\Models\User;
use App\Enums\UserRole;
use App\DTO\User\UserDTO;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users with pagination and search
     *
     * @param array $params
     * @return array
     */
    public function getAllUsers(array $params = []): array
    {
        try {
            $perPage = $params['per_page'] ?? 15;
            $page = $params['page'] ?? 1;
            $search = $params['search'] ?? null;

            $users = $this->userRepository->getAllUsers([
                'per_page' => (int)$perPage,
                'page' => (int)$page,
                'search' => $search
            ]);

            return [
                'success' => true,
                'data' => $users->toPaginatedArray()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode() ?: 500
            ];
        }
    }

    /**
     * Get user by ID and return as DTO
     *
     * @param int $id
     * @return UserDTO|null
     */
    public function getUserById($id): ?UserDTO
    {
        $user = $this->userRepository->getUserById($id);
        return $user ? UserDTO::fromModel($user) : null;
    }

    /**
     * Update user with role-based access control
     *
     * @param array $data
     * @param int $userId
     * @param User $currentUser
     * @return array
     */
    public function updateUser(array $data, int $userId, User $currentUser): array
    {
        try {
            $user = $this->userRepository->getUserById($userId);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'code' => 404
                ];
            }

            $updatableFields = ['name', 'email', 'phone_number'];
            $updateData = array_intersect_key($data, array_flip($updatableFields));
            $isPrivilegedUser = in_array($currentUser->role, [UserRole::ADMIN->value, UserRole::HR->value]);

            if ($isPrivilegedUser) {
                if (isset($data['role'])) {
                    $updateData['role'] = $data['role'];
                }
                if (isset($data['status'])) {
                    $updateData['status'] = $data['status'];
                }
            }
            if (isset($data['password'])) {
                if (Hash::check($data['password'], $user->password)) {
                    return [
                        'success' => false,
                        'message' => 'New password cannot be the same as old password',
                        'code' => 400
                    ];
                }
                if ($isPrivilegedUser) {
                    $updateData['password'] = Hash::make($data['password']);
                } else {
                    if (Hash::check($data['old_password'], $user->password)) {
                        $updateData['password'] = Hash::make($data['password']);
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Old password is incorrect',
                            'code' => 400
                        ];
                    }
                }
            }
            $updated = $this->userRepository->updateUser($user, $updateData);

            if ($updated) {
                $user = $user->fresh();

                return [
                    'success' => true,
                    'data' => UserDTO::fromModel($user),
                    'message' => 'User updated successfully'
                ];
            }

            throw new \Exception('Failed to update user');
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage(),
                'code' => $th->getCode() ?: 500,
                'exception' => $th
            ];
        }
    }
}
