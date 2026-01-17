<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    // 日次勤怠一覧
    public function index(Request $request) 
    {
        // 表示日（未指定なら今日
        $date = $request->query('date')
            ?Carbon::createFromFormat('Y-m-d',$request->query('date'))
            :Carbon::today();

            // その日の全スタッフの勤怠
        $attendances = Attendance::with('user_id')
            ->whereDate('date', $date)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendance.index',[
            'attendances' => $attendances,
            'currentDate' => $date,
        ]);
    }

    // 勤怠詳細
    public function detail($id) 
    {
        // ① 勤怠を取得（管理者なので user_id 制限なし）
        $attendance = Attendance::with('user')
        ->find($id);
        if (!$attendance){
            abort(404);
        }

        // ② 休憩取得
        $breakTimes = BreakTime::where('attendance_id', $attendance->id)
        ->orderBy('start_time')
        ->get();

        // ③ 修正申請状態
        $correction = AttendanceCorrectionRequest::where('attendance_id',$attendance->id)
        ->latest()
        ->first();
        $isPending = $correction && $correction->status === 'pending';


        return view('admin.attendance.detail',compact('attendance','breakTimes','correction','isPending'));
    }

    // 管理者修正
    public function update() { }

    // スタッフ別勤怠
    public function staff() { }

}

