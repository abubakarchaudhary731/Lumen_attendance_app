<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Working Hours Configuration
    |--------------------------------------------------------------------------
    |
    | This value determines the default working hours and office timings
    | used by the attendance system.
    |
    */

    'working_hours' => env('WORKING_HOURS', 8),
    'office_start_time' => env('OFFICE_START_TIME', '09:00:00'),
    'office_end_time' => env('OFFICE_END_TIME', '17:00:00'),

    /*
    |--------------------------------------------------------------------------
    | Grace Periods
    |--------------------------------------------------------------------------
    |
    | These values determine the grace periods for check-in and check-out.
    |
    */
    'check_in_grace_minutes' => 15,  // Grace period for late check-in
    'check_out_grace_minutes' => 30,    // Hours after office end time to mark as missed checkout

    /*
    |--------------------------------------------------------------------------
    | Half Day Threshold
    |--------------------------------------------------------------------------
    |
    | Minimum working hours required to be considered a half day.
    |
    */
    'half_day_hours' => 4,
];
