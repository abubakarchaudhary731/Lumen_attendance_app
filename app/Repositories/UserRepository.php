<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\Users\UserStatus;
use App\DTO\User\UserDTO;
use App\DTO\User\UserCollectionDTO;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * Create a new user
     *
     * @param array $data
     * @return UserDTO
     */
    public function createUser(array $data): UserDTO
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => UserStatus::ACTIVE->value,
            'role' => $data['role'],
            'phone_number' => $data['phone_number'] ?? null,
        ]);

        return UserDTO::fromModel($user);
    }

    /**
     * Find user by email and return as DTO
     *
     * @param string $email
     * @return UserDTO|null
     */
    public function findByEmail(string $email): ?UserDTO
    {
        $user = User::where('email', $email)->first();

        return $user ? UserDTO::fromModel($user) : null;
    }

    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getUserById($id): ?User
    {
        return User::find($id);
    }

    /**
     * Get all users with pagination and search
     *
     * @param array $params
     * @return UserCollectionDTO
     */
    public function getAllUsers(array $params = []): UserCollectionDTO
    {
        $perPage = $params['per_page'] ?? 15;
        $page = $params['page'] ?? 1;
        $search = $params['search'] ?? null;
        $excludeCurrentUser = $params['exclude_current'] ?? true;

        $query = User::query();

        if ($excludeCurrentUser && auth()->check()) {
            $query->where('id', '!=', auth()->id());
        }

        // Apply search filter if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Get paginated results
        $paginator = $query->paginate(
            perPage: (int)$perPage,
            columns: ['*'],
            pageName: 'page',
            page: (int)$page
        );

        return UserCollectionDTO::fromPaginator($paginator);
    }

    /**
     * Update user data
     *
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }
}
