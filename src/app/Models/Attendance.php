<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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
}
