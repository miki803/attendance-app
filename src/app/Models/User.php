<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

     // ユーザーは勤怠をたくさん持つ
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

     // ユーザーは修正申請をたくさん出す
    public function attendanceCorrectionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }

    //手入力されるカラム
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    //セキュリティ用
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //メール認証
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
