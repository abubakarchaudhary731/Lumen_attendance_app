<?php

namespace App\Services;

use Carbon\Carbon;
use App\Repositories\AttendanceRepository;

class AttendanceService
{
    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    public function checkIn($user, $validatedData)
    {
        $existingCheckIn = $this->attendanceRepository->existingCheckInRecord($user);

        if ($existingCheckIn) {
            return [
                'success' => false,
                'message' => 'You have already checked in today',
                'code' => 400
            ];
        }

        $checkedIn = $this->attendanceRepository->createCheckInRecord($user, $validatedData);
        if ($checkedIn) {
            return [
                'success' => true,
                'message' => 'Checked in successfully',
                'data' => $checkedIn->toArray(),
            ];
        }
    }

    public function checkOut($user, $validatedData)
    {
        $checkOutTime = Carbon::now();
        $attendance = $this->attendanceRepository->checkOut($user, $checkOutTime, $validatedData);

        if (!$attendance) {
            return [
                'success' => false,
                'message' => 'No active check-in found for today',
                'code' => 400
            ];
        }

        return [
            'success' => true,
            'message' => 'Checked out successfully',
            'data' => $attendance->toArray()
        ];
    }
}
