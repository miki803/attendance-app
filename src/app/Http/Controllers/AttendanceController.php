<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;

class AttendanceController extends Controller
{
    // 打刻画面
    public function index()
    {
        //出勤時登録画面
        $attendance = Attendance::where('user_id',auth()->id())
            ->whereDate('date',now()->toDateString())
            ->first();

        $onBreak =false;

        //この勤怠に紐づく休憩があるか
        if($attendance){
            $onBreak = BreakTime::where('attendance_id',$attendance->id)
                ->whereNull('end_time')
                ->exists();
        }
        return view('attendance.index',compact('attendance','onBreak'));
    }

    // 出勤
    public function start()
    {
        $already = Attendance::where('user_id',auth()->id())
            ->whereDate('date',now()->toDateString())
            ->exists();
        if($already){
            abort(400);
        }

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
            abort(400);
        }
        $attendance->update([
            'end_time' => now()->format('H:i'),
            'status' => 'finished',
        ]);
        return redirect('/attendance');// 一覧へ
    }

    // 勤怠一覧（月次）
    public function list(Request $request)
    {
        // 表示する月（YYYY-MM）
        $month = $request->query('month')
            ?Carbon::createFromFormat('Y-m',$request->query('month'))
            :Carbon::now();

        // 月初・月末
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        //月の日付一覧を作る
        $dates =[];
        $currentDate = $startOfMonth->copy();
        while ($currentDate <= $endOfMonth) {
            $dates[] = $currentDate->copy();
            $currentDate->addDay();
        }

        // 勤怠を日付キーで取得
        $attendances = Attendance::where('user_id', auth()->id())
        ->whereBetween('date',[$startOfMonth,$endOfMonth])
        ->with('breakTimes')
        ->orderBy('date')
        ->get()
        ->keyBy(function ($attendance) {
            return $attendance->date->format('Y-m-d');
        });

        return view('attendance.list',[
            'dates' => $dates,
            'attendances' => $attendances,
            'currentMonth' => $month,
        ]);

    }

    // 勤怠詳細
    public function detail($id)
    {
        $user = auth()->user();

        $attendance = Attendance::with(['breakTimes'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $isPending = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->exists();

        return view('attendance.detail', [
            'attendance' => $attendance,
            'breakTimes' => $attendance->breakTimes,
            'isPending' => $isPending,
        ]);
    }

    public function detailByDate($date)
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->with('breakTimes')
            ->first();

        // breakTimes（null対策）
        $breakTimes = $attendance?->breakTimes ?? collect();

        // 承認待ち判定（attendanceがある場合のみ）
        $isPending = false;
        if ($attendance) {
            $isPending = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
                ->where('status', 'pending')
                ->exists();
        }

        return view('attendance.detail', compact(
            'attendance',
            'date',
            'breakTimes',
            'isPending'
        ));
    }

}
