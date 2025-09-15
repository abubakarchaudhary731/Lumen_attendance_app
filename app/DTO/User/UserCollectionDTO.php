<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserCollectionDTO extends BaseDTO
{
    /**
     * @param array<UserDTO> $users
     */
    public function __construct(
        public readonly array $users,
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
        public readonly ?int $lastPage = null,
    ) {}

    /**
     * Create from paginator instance
     *
     * @param LengthAwarePaginator $paginator
     * @return static
     */
    public static function fromPaginator(LengthAwarePaginator $paginator): self
    {
        $userDTOs = $paginator->getCollection()->map(function (User $user) {
            return UserDTO::fromModel($user);
        })->toArray();

        return new self(
            users: $userDTOs,
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage()
        );
    }

    /**
     * Convert to array with pagination meta
     *
     * @return array
     */
    public function toPaginatedArray(): array
    {
        return [
            'data' => $this->users,
            'meta' => [
                'total' => $this->total,
                'per_page' => $this->perPage,
                'current_page' => $this->currentPage,
                'last_page' => $this->lastPage,
            ]
        ];
    }
}
