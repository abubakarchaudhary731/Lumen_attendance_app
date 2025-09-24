<?php

namespace App\Services;

use Carbon\Carbon;
use App\Enums\Users\UserRole;
use App\Repositories\AttendanceRepository;
use App\Enums\Attendance\AttendanceStatus;

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
            throw new \Exception('You have already checked in today', 400);
        }

        $checkedIn = $this->attendanceRepository->createCheckInRecord($user, $validatedData);
        if ($checkedIn) {
            return [
                'message' => 'Checked in successfully',
                'data' => $checkedIn->toArray(),
            ];
        }
    }

    /**
     * Update attendance record with validation and business logic
     *
     * @param int $id
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function updateAttendance($id, array $data)
    {
        $attendance = $this->attendanceRepository->getAttendanceById($id);

        if ($attendance->status === AttendanceStatus::CHECKED_IN->value) {
            throw new \Exception('You can only update records after checkout', 400);
        }

        $allowedFields = ['check_in', 'check_out', 'is_work_from_home', 'status', 'notes'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        // Helper to merge date (from existing) with time (from request)
        $mergeDateTime = function ($existingDateTime, $newTime = null) {
            if (!$newTime) {
                return Carbon::parse($existingDateTime); // keep old value
            }
            $date = Carbon::parse($existingDateTime)->format('Y-m-d');
            return Carbon::parse("$date $newTime");
        };

        // If check_in or check_out is being updated
        if (isset($updateData['check_in']) || isset($updateData['check_out'])) {
            $checkIn = $mergeDateTime($attendance->check_in, $updateData['check_in'] ?? null);
            $checkOut = $attendance->check_out
                ? $mergeDateTime($attendance->check_out, $updateData['check_out'] ?? null)
                : $mergeDateTime($attendance->check_in, $updateData['check_out'] ?? null);

            $updateData['check_in'] = $checkIn;

            if ($checkOut) {
                $updateData['check_out'] = $checkOut;
                $updateData['total_hours'] = round($checkIn->diffInMinutes($checkOut) / 60, 2);
            }

            $updateData['is_late'] = $this->attendanceRepository->calculateIsLate($checkIn);
        }

        $updatedAttendance = $this->attendanceRepository->updateAttendanceRecord($id, $updateData);

        return [
            'message' => 'Attendance record updated successfully',
            'data' => $updatedAttendance->toArray()
        ];
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
            'data' => $attendances,
        ];
    }

    public function checkOut($user, $validatedData)
    {
        $checkOutTime = Carbon::now();
        $attendance = $this->attendanceRepository->checkOut($user, $checkOutTime, $validatedData);

        if (!$attendance) {
            throw new \Exception('No active check-in found for today', 400);
        }

        return [
            'message' => 'Checked out successfully',
            'data' => $attendance->toArray()
        ];
    }
}
