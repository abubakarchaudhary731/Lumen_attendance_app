<?php

namespace App\Services;

use Carbon\Carbon;
use App\Enums\Users\UserRole;
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

    /**
     * List attendances with optional filters
     *
     * @param mixed $user The authenticated user
     * @param array $params Request parameters
     * @return array
     */
    public function listAttendances($user, array $params = []): array
    {
        if (!in_array($user->role, [UserRole::ADMIN->value, UserRole::HR->value])) {
            $params['user_id'] = $user->id;
        } else {
            if (isset($params['is_all_attendance']) && $params['is_all_attendance'] == 'true') {
                $params['show_all_attendances'] = true;
            } elseif (isset($params['user_id'])) {
                $params['user_id'] = $params['user_id'];
            } else {
                $params['user_id'] = $user->id;
            }
        }

        unset($params['is_all_attendance']);

        $attendances = $this->attendanceRepository->listAttendances($params);

        return [
            'success' => true,
            'data' => $attendances,
        ];
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
