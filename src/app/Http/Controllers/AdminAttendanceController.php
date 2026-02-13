<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use Carbon\Carbon;
use App\Http\Requests\AdminAttendanceRequest;
use Illuminate\Support\Facades\DB;

class AdminAttendanceController extends Controller
{
    // 日次勤怠一覧
    public function index(Request $request) 
    {
        // 表示日（未指定なら今日
        $currentDate = $request->query('date')

            ? Carbon::createFromFormat('Y-m-d', $request->query('date'))
            : Carbon::today();

            // 一般ユーザーを全員取得
        $users = User::where('is_admin', false)
            ->with(['attendances' => function ($query) use ($currentDate) {
                $query->whereDate('date', $currentDate);
            }])
            ->orderBy('id')
            ->get();

        return view('admin.attendance.index',[
            'users' => $users,
            'currentDate' => $currentDate,
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
    public function update(AdminAttendanceRequest $request) 
    {

        $userId = $request->user_id ?? auth()->id();

        if ($request->attendance_id) {
            $attendance = Attendance::findOrFail($request->attendance_id);
        } else {
            $attendance = Attendance::firstOrCreate(
                [
                    'user_id' => $userId,
                    'date'    => $request->date,
                ],
                [
                    'status' => 'normal',
                ]
        );;
        }

        DB::transaction(function () use ($request, $attendance){
            // 出勤退勤更新
            $attendance->update([
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
            ]);
            // 休憩リセット
            $attendance->breakTimes()->delete();
            foreach ($request->breaks ?? [] as $break) {
                if (empty($break['start']) && empty($break['end'])) continue;

                $attendance->breakTimes()->create([
                    'start_time' => $break['start'],
                    'end_time'   => $break['end'],
                ]);
            }
        });
        return redirect()
            ->route('admin.attendance.detail', $attendance->id)
            ->with('success', '更新しました');
    }

    // スタッフ別勤怠
    public function staff($id, Request $request) {
        // スタッフ取得
        $staff = User::findOrFail($id);

        // 表示する月（YYYY-MM）
        $currentMonth = $request->query('month')
            ?Carbon::createFromFormat('Y-m',$request->query('month'))
            :Carbon::now();

        // 月初・月末
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        //日付一覧
        $dates =[];
        $currentDate = $startOfMonth->copy();
            while ($currentDate <= $endOfMonth) {
                $dates[] = $currentDate->copy();
                $currentDate->addDay();
            }

        // 勤怠取得（スタッフ × 月）
        $attendances = Attendance::where('user_id', $staff->id)
            ->whereBetween('date',[$startOfMonth,$endOfMonth])
            ->with('breakTimes')
            ->orderBy('date')
            ->get()
            ->keyBy(function ($attendance) {
                return $attendance->date->format('Y-m-d');
        });

        return view('admin.attendance.staff',[
            'staff' =>$staff,
            'dates' => $dates,
            'attendances' => $attendances,
            'currentMonth' => $currentMonth,
        ]);

    }

    public function detailByDate(User $user, $date)
    {
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->with('breakTimes')
            ->first();

        $breakTimes = $attendance?->breakTimes ?? collect();

        $correction = null;
        $isPending = false;
        if ($attendance) {
            $correction = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
                ->latest()
                ->first();

            $isPending = $correction && $correction->status === 'pending';
        }

        return view('admin.attendance.detail', compact(
            'attendance',
            'breakTimes',
            'correction',
            'isPending',
            'user',
            'date'
        ));
    }


}

