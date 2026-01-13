<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Attendance extends Model
{
    use HasFactory;

    // この勤怠は誰のもの？
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     // この勤怠の休憩（複数）
    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    // この勤怠の修正申請（複数）
    public function correctionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];
    protected $casts = [
        'date' => 'date'
    ];

    // 出勤時刻（H:MM）
    public function getStartTimeFormattedAttribute()
    {
        if (!$this->start_time) {
            return null;
        }
        return Carbon::parse($this->start_time)->format('H:i');
    }

    // 退勤時刻（H:MM ）
    public function getEndTimeFormattedAttribute()
    {
        if (!$this->end_time) {
            return null;
        }
        return Carbon::parse($this->end_time)->format('H:i');
    }

    //休憩合計（H:MM）
    public function getBreakTimeAttribute()
    {
        $totalMinutes = 0;

        foreach ($this->breakTimes as $break) {
            if ($break->start_time && $break->end_time) {

                $start = Carbon::parse($break->start_time);
                $end = Carbon::parse($break->end_time);

                $totalMinutes += $start->diffInMinutes($end);
            }
        }
        if ($totalMinutes === 0) {
            return null;
        }
        return sprintf('%d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60);
    }

    //勤務時間（H:MM）
    public function getWorkingTimeAttribute()
    {
        if(!$this->start_time || !$this->end_time) {
            return null;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        $totalMinutes = $start->diffInMinutes($end);

        foreach ($this->breakTimes as $break) {
            if ($break->start_time && $break->end_time) {
                $bStart = Carbon::parse($break->start_time);
                $bEnd = Carbon::parse($break->end_time);

                $totalMinutes -= $bStart->diffInMinutes($bEnd);
            }
        }
        if ($totalMinutes <= 0) {
            return null;
        }
        return sprintf('%d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60);
    }
}
