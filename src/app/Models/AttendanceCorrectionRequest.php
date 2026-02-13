<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakCorrections()
    {
        return $this->hasMany(
            AttendanceCorrectionDetail::class,
            'request_id'
            );
    }
    public function details()
    {
        return $this->hasMany(
            \App\Models\AttendanceCorrectionDetail::class,
            'request_id'
        );
    }


}


