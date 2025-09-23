<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'check_in',
        'check_out',
        'total_hours',
        'status',
        'notes',
        'is_missed_checkout',
        'is_work_from_home',
        'is_late',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_hours' => 'decimal:2',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return Carbon::instance($date)->setTimezone(
            new \DateTimeZone(config('app.timezone', 'UTC'))
        )->format('Y-m-d H:i:s');
    }

    /**
     * Get the user that owns the attendance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
