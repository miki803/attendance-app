<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    // 打刻画面
    public function index()
    {
        //出勤時登録画面
        return view('attendance.index');
    }

    // 出勤
    public function start()
    {
        Attendance::create([
            'user_id' => auth()->id(),
            'date' => now()->toDateString(), // attendance を作成 or 更新
            'start_time' => now()->format('H:i'),// 今日の日付
            'status' => 'working',// status = working
        ]);
        return redirect('/attendance');// 一覧 or 打刻画面へ戻す

    }

    // 休憩
    public function breakStart()
    {
        // 出勤中の勤怠を取得
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->whereNull('end_time')
            ->first();
        if (!$attendance){
            abort(400); // 出勤してないのに休憩はできない
        }
        // 休憩開始
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => now()->format('H:i'),
        ]);
        return redirect('/attendance');
    }

    public function breakEnd()
    {
        // 出勤中の勤怠を取得
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->whereNull('end_time')
            ->first();
        if (!$attendance){
            abort(400);
        }
        // 休憩中のレコードを取得
        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->first();
        if (!$break){
            abort(400);
        }
        // 休憩終了
        $break->update([
            'end_time' => now()->format('H:i'),
        ]);
        return redirect('/attendance');
    }

    // 退勤
    public function end()
    {
        // 出勤中の勤怠を取得
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', now()->toDateString())
            ->whereNull('end_time')
            ->first();

        if (!$attendance) {
            abort(400); // 出勤してない
        }
        $attendance->update([
            'end_time' => now()->format('H:i'),// attendance の end_time を入れる
            'status' => 'finished',// status = finished
        ]);
        return redirect('/attendance');// 一覧へ
    }

    // 勤怠一覧
    public function list()
    {
        //ログイン中のユーザーの勤怠データをDBから取って、一覧画面に渡す
        $attendances = Attendance::where('user_id', auth()->id()) ->orderBy('date', 'desc')
        ->get();
        return view('attendance.list', compact('attendances'));

    }

    // 勤怠詳細
    public function detail($id)
    {
        // URLで指定した$idの勤怠の中から、ログイン中ユーザーのものだけを1件取得する
        $attendance = Attendance::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();
        if (!$attendance){
            abort(404);
        }

        // この勤怠（$attendance）に紐づく休憩を、開始時間順で全部取る
        $breakTimes = BreakTime::where('attendance_id', $attendance->id)
        ->orderBy('start_time')
        ->get();
        return view('attendance.detail',compact('attendance','breakTimes'));
    }
}
