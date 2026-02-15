<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionDetail;
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
            ->get();

        foreach ($attendances as $attendance) {
            $pending = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pending) {
                $details = AttendanceCorrectionDetail::where('request_id', $pending->id)->get();
                foreach ($details as $detail) {
                    if ($detail->target === 'attendance') {
                        $attendance->start_time = $detail->start_time;
                        $attendance->end_time   = $detail->end_time;
                    }
                }
            }
        }
        $attendances = $attendances->keyBy(function ($attendance) {
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

        $pendingRequest = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $isPending = false;
        $breakTimes = $attendance->breakTimes;

        if ($pendingRequest) {
            $isPending = true;
            $details = AttendanceCorrectionDetail::where('request_id', $pendingRequest->id)->get();
            $breakTimes = collect();

            foreach ($details as $detail){
                // 出勤退勤
                if ($detail->target === 'attendance') {
                    $attendance->start_time = $detail->start_time;
                    $attendance->end_time   = $detail->end_time;
                    $attendance->remark     = $detail->note;
                }
                // 休憩
                if ($detail->target === 'break') {
                    $breakTimes->push((object)[
                        'start_time' => $detail->start_time,
                        'end_time'   => $detail->end_time,
                    ]);
                }
            }
            if ($breakTimes->isEmpty()) {
                $breakTimes = $attendance->breakTimes;
            }
        }

        return view('attendance.detail', compact(
            'attendance',
            'breakTimes',
            'isPending'
        ));
    }

    public function detailByDate($date)
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', $date)
            ->with('breakTimes')
            ->first();

        $breakTimes = $attendance?->breakTimes ?? collect();
        $isPending = false;

        if ($attendance) {
            $pendingRequest = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pendingRequest) {
                $isPending = true;
                $details = AttendanceCorrectionDetail::where('request_id', $pendingRequest->id)->get();
                $breakTimes = collect();

                foreach ($details as $detail) {
                    // 出勤退勤
                    if ($detail->target === 'attendance') {
                        $attendance->start_time = $detail->start_time;
                        $attendance->end_time   = $detail->end_time;
                        $attendance->remark     = $detail->note;
                    }
                    // 休憩
                    if ($detail->target === 'break') {
                        $breakTimes->push((object)[
                            'start_time' => $detail->start_time,
                            'end_time'   => $detail->end_time,
                        ]);
                    }
                }
                if ($breakTimes->isEmpty()) {
                    $breakTimes = $attendance->breakTimes;
                }
            }
        }

        return view('attendance.detail', compact(
            'attendance',
            'date',
            'breakTimes',
            'isPending'
        ));
    }

}
