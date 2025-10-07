<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStandup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_stand_up';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'day_start',
        'day_end',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return Carbon::instance($date)->setTimezone(
            new \DateTimeZone(config('app.timezone', 'UTC'))
        )->format('Y-m-d H:i:s');
    }

    /**
     * Get the user that owns the Standup record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
