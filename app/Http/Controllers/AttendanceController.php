<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Check in a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();

        // Check if user has already checked in today
        $existingCheckIn = Attendance::where('user_id', $user->id)
            ->whereDate('check_in', Carbon::today())
            ->first();

        if ($existingCheckIn) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already checked in today'
            ], 400);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'check_in' => Carbon::now(),
            'status' => 'PRESENT'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Checked in successfully',
            'attendance' => $attendance
        ], 201);
    }

    /**
     * Check out a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in', Carbon::today())
            ->whereNull('check_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active check-in found for today'
            ], 400);
        }

        $checkOutTime = Carbon::now();
        $attendance->update([
            'check_out' => $checkOutTime,
            'total_hours' => $checkOutTime->diffInHours($attendance->check_in, true)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Checked out successfully',
            'attendance' => $attendance
        ]);
    }

    /**
     * Display a listing of the attendance records.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $query = Attendance::with('user');

        // If user is not admin, only show their own attendance
        if ($user->role !== 'ADMIN') {
            $query->where('user_id', $user->id);
        }

        $attendance = $query->orderBy('check_in', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'attendance' => $attendance
        ]);
    }

    /**
     * Display the specified attendance record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        $user = Auth::user();

        // Only allow admin or the user who owns the attendance record to view it
        if ($user->role !== 'ADMIN' && $attendance->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'attendance' => $attendance
        ]);
    }
}
