<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;

    // 休憩は1つの勤怠に属する
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    protected $fillable = [
        'attendance_id',
        'start_time',
        'end_time',
    ];
}
