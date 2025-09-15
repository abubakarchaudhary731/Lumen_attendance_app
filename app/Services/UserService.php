<?php

namespace App\Services;

use App\Repositories\UserRepository;

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
}
