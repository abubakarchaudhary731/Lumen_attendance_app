<?php

namespace App\DTO;

class PaginationMetaDTO extends BaseDTO
{
    public function __construct(
        public readonly int $current_page,
        public readonly ?int $from,
        public readonly int $last_page,
        public readonly int $per_page,
        public readonly ?int $to,
        public readonly int $total,
    ) {}
}
