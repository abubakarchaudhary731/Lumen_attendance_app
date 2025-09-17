<?php

namespace App\Enums\Attendance;

enum AttendanceStatus: string
{
    case CHECKED_IN = 'CHECKED_IN';
    case PRESENT = 'PRESENT';
    case ABSENT = 'ABSENT';
    case LATE = 'LATE';
    case LEAVE = 'LEAVE';
    case HOLIDAY = 'HOLIDAY';
    case HALF_DAY = 'HALF_DAY';
    case OVERTIME = 'OVERTIME';
    case MISSED_CHECKOUT = 'MISSED_CHECKOUT';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
