<?php

namespace App\Enums\Attendance;

enum AttendanceStatus: string
{
    case CHECKED_IN = 'CHECKED_IN';
    case PRESENT = 'PRESENT';
    case ABSENT = 'ABSENT';
    case LEAVE = 'LEAVE';
    case HOLIDAY = 'HOLIDAY';
    case HALF_DAY = 'HALF_DAY';
    case OVERTIME = 'OVERTIME';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
