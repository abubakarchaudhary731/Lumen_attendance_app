<?php

namespace App\DTO\Attendance;

use App\DTO\BaseDTO;
use App\DTO\PaginationMetaDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatedAttendanceResponseDTO extends BaseDTO
{
    public function __construct(
        public readonly array $data,
        public readonly PaginationMetaDTO $meta,
    ) {}

    /**
     * Create a new DTO instance from a LengthAwarePaginator
     */
    public static function fromPaginator(LengthAwarePaginator $paginator): self
    {
        return new self(
            data: $paginator->items(),
            meta: new PaginationMetaDTO(
                current_page: $paginator->currentPage(),
                from: $paginator->firstItem(),
                last_page: $paginator->lastPage(),
                per_page: $paginator->perPage(),
                to: $paginator->lastItem(),
                total: $paginator->total(),
            )
        );
    }
}
