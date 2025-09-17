<?php

namespace App\Enums\Users;

enum UserStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
