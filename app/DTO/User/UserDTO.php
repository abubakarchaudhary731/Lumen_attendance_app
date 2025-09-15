<?php

namespace App\DTO\User;

use App\Models\User;
use App\DTO\BaseDTO;
use App\Enums\UserRole;
use App\Enums\UserStatus;

class UserDTO extends BaseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone_number,
        public readonly UserRole $role,
        public readonly UserStatus $status,
        public readonly ?string $created_at = null,
        public readonly ?string $updated_at = null,
    ) {}

    /**
     * Create UserDTO from User model
     *
     * @param User $user
     * @return self
     */
    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            phone_number: $user->phone_number,
            role: UserRole::from($user->role),
            status: UserStatus::from($user->status),
            created_at: $user->created_at?->toDateTimeString(),
            updated_at: $user->updated_at?->toDateTimeString(),
        );
    }
}
