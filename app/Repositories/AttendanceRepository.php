<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Enums\Attendance\AttendanceStatus;

class AttendanceRepository
{
    public function existingCheckInRecord($user)
    {
        return Attendance::where('user_id', $user->id)
            ->whereDate('check_in', Carbon::today())
            ->first();
    }

    public function createCheckInRecord($user, $validatedData)
    {
        return Attendance::create([
            'user_id' => $user->id,
            'check_in' => Carbon::now(),
            'notes' => $validatedData['notes'] ?? null,
            'status' => AttendanceStatus::CHECKED_IN->value,
            'is_late' => $this->calculateIsLate(Carbon::now()),
            'is_work_from_home' => $validatedData['is_work_from_home'],
        ]);
    }

    public function getTodayCheckIn($userId)
    {
        return Attendance::where('user_id', $userId)
            ->whereDate('check_in', Carbon::today())
            ->whereNull('check_out')
            ->first();
    }

    public function listAttendances($params = [])
    {
        $query = Attendance::with(['user' => function ($query) use ($params) {
            $selectFields = ['id', 'name', 'email', 'role', 'status'];
            $query->select($selectFields);

            if (isset($params['show_all_attendances']) && $params['show_all_attendances'] === true) {
                $query->where('status', 'active');
            }
        }]);

        if (isset($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }

        if (isset($params['start_date'])) {
            $query->whereDate('check_in', '>=', $params['start_date']);
        }

        if (isset($params['end_date'])) {
            $query->whereDate('check_in', '<=', $params['end_date']);
        }

        if (!isset($params['start_date']) && !isset($params['end_date'])) {
            $now = Carbon::now();
            $query->whereMonth('check_in', $now->month)
                ->whereYear('check_in', $now->year);
        }

        $query->orderBy('check_in', 'desc');

        $perPage = $params['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    public function checkOut($user, $checkOutTime, $validatedData)
    {
        $attendance = $this->getTodayCheckIn($user->id);

        if (!$attendance) {
            return null;
        }

        $checkInTime = Carbon::parse($attendance->check_in);
        $totalMinutes = $checkOutTime->diffInMinutes($checkInTime, true);
        $totalHours   = round($totalMinutes / 60, 2);

        $status = $this->calculateCheckOutStatus(
            $checkOutTime,
            $totalHours,
            $validatedData['is_half_day'],
            $validatedData['is_overtime']
        );

        return tap($attendance)->update([
            'check_out' => $checkOutTime,
            'total_hours' => $totalHours,
            'status' => $status,
            'notes' => $validatedData['notes'] ?? $attendance->notes,
        ]);
    }

    protected function calculateIsLate(Carbon $checkInTime): bool
    {
        $officeStartTime = config('attendance.office_start_time');

        // Create a Carbon instance for today with the office start time
        $officeStart = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $checkInTime->format('Y-m-d') . ' ' . $officeStartTime
        );
        $officeStartGrace = $officeStart->copy()->addMinutes(
            (int) config('attendance.check_in_grace_minutes')
        );

        return $checkInTime->gt($officeStartGrace);
    }

    protected function calculateCheckOutStatus(Carbon $checkOutTime, float $totalHours, bool $isHalfDay, bool $isOvertime): string
    {
        $officeEndTimeStr = config('attendance.office_end_time');
        $officeEndTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $checkOutTime->format('Y-m-d') . ' ' . $officeEndTimeStr
        );

        $halfDayHours = config('attendance.half_day_hours');
        $graceCheckOutMinutes = config('attendance.check_out_grace_minutes');
        $workingHours = config('attendance.working_hours');

        if ($isHalfDay || abs($totalHours - $halfDayHours) <= 1) {
            return AttendanceStatus::HALF_DAY->value;
        }

        if (
            $isOvertime ||
            ($checkOutTime->gt($officeEndTime->copy()->addMinutes($graceCheckOutMinutes)) && $totalHours > $workingHours)
        ) {
            return AttendanceStatus::OVERTIME->value;
        }

        return AttendanceStatus::PRESENT->value;
    }

    protected function isMissedCheckout(Carbon $checkOutTime): bool
    {
        $officeEndTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            Carbon::now()->format('Y-m-d') . ' ' . config('attendance.office_end_time')
        );
        $graceHours = config('attendance.check_out_grace_hours', 2);
        return $checkOutTime->gt($officeEndTime->copy()->addHours($graceHours));
    }
}
