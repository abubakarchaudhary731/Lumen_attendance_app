<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\DailyStandup;

class DailyStandupRepository
{
    public function createDailyStandup($data)
    {
        return DailyStandup::create($data);
    }

    public function getTodayRecord(int $userId)
    {
        return DailyStandup::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->first();
    }
}
