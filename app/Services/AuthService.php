<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            /** @var User $user */
            $user = $this->userRepository->createUser($data);

            return [
                'user' => $user->toArray(),
                'message' => 'User Registered Successfully'
            ];
        });
    }

    public function login(array $data): array
    {
        $user = $this->userRepository->getUserByEmail($data['email']);
        if (!$user) {
            throw new \Exception('User Not Found', 401);
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw new \Exception('Incorrect Password', 401);
        }

        $token = auth('api')->login($user);
        if (!$token) {
            throw new \Exception('Could not create token', 500);
        }

        return [
            'user' => $user->toArray(),
            'token' => $this->respondWithToken($token),
        ];
    }

    public function respondWithToken($token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ];
    }
}
