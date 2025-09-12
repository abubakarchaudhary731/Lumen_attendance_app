<?php

namespace App\Enums;

enum UserRole: string
{
    case EMPLOYEE = 'EMPLOYEE';
    case HR = 'HR';
    case ADMIN = 'ADMIN';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
