<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreStampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    // 一般ユーザー：申請一覧
    public function userList(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = AttendanceCorrectionRequest::where('user_id', auth()->id())
            ->where('status', $status)
            ->with(['attendance', 'user'])
            ->orderByDesc('created_at')
            ->get();

        return view('correction.user_list', compact('requests','status'));
    }

    // 一般ユーザー：修正申請送信
    public function store(StoreStampCorrectionRequest $request)
    {
        // attendance取得 or 作成
        if ($request->attendance_id) {
            $attendance = Attendance::where('id', $request->attendance_id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        } else {
            $attendance = Attendance::create([
                'user_id' => auth()->id(),
                'date'    => $request->date,
                'status'  => 'pending',
            ]);
        }

        // 承認待ちチェック
        $existsPending = AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->exists();

        if ($existsPending){
            return back()
                ->withErrors(['message' => '承認待ちのため修正できません。'])
                ->withInput();
        }

        return DB::transaction(function () use ($request, $attendance) {

            $correctionRequest = AttendanceCorrectionRequest::create([
                'user_id'       => auth()->id(),
                'attendance_id' => $attendance->id,
                'status'        => 'pending',
                ]);

            // 出勤・退勤
            $correctionRequest->details()->create([
                'target'     => 'attendance',
                'start_time' => $request->start_time,
                'end_time'   => $request->end_time,
                'note'       => $request->remark,
            ]);

            // 休憩
            foreach ($request->requested_breaks ?? [] as $break) {
                if (empty($break['start']) && empty($break['end'])) {
                    continue;
                }
                $correctionRequest->details()->create([
                    'target'     => 'break',
                    'start_time' => $break['start'],
                    'end_time'   => $break['end'],
                    'note'       => $request->remark,
                ]);
            }
            return redirect('/stamp_correction_request/list')
                ->with('success', '修正申請を送信しました。');
        });
    }


    // 一般ユーザー：申請詳細（閲覧のみ）
    public function userShow($id)
    {
        $correction = AttendanceCorrectionRequest::with(['attendance', 'breakCorrections'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('correction.user_show', compact('correction'));
    }


    // 管理者：申請一覧
    public function adminList(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = AttendanceCorrectionRequest::with([
            'attendance',
            'user',
            'details'
            ])
            ->where('status', $status)
            ->orderByDesc('created_at')
            ->get();

        return view('correction.admin_list', compact('requests', 'status'));
    }

    // 管理者：申請詳細（承認画面）
    public function adminShow($id)
    {
        $correction = AttendanceCorrectionRequest::with([
            'attendance',
            'user',
            'details'
        ])->findOrFail($id);

        $attendanceDetail = $correction->details
            ->where('target', 'attendance')
            ->first();
        
        $breakDetails = $correction->details
            ->where('target', 'break');

        return view('correction.approve', compact(
            'correction',
            'attendanceDetail',
            'breakDetails'
            ));
    }

    // 管理者：申請承認
    public function approve($id)
    {
        $correction = AttendanceCorrectionRequest::with(['attendance', 'details'])
            ->findOrFail($id);
        if ($correction->status === 'approved') {
            return back()->withErrors('すでに承認済みです');
        }

        DB::transaction(function () use ($correction) {
            $attendance = $correction->attendance;
            $details = $correction->details;

            // 出勤退勤
            $main = $details->where('target', 'attendance')->first();
            if ($main) {
                $attendance->update([
                    'start_time' => $main->start_time,
                    'end_time'   => $main->end_time,
                ]);
            }

            // 休憩
            $attendance->breakTimes()->delete();
            $breaks = $details->where('target', 'break');
            foreach ($breaks as $break) {
                $attendance->breakTimes()->create([
                    'start_time' => $break->start_time,
                    'end_time'   => $break->end_time,
                ]);
            }
            $correction->update([
                'status' => 'approved',
            ]);
        });
        return redirect('/admin/stamp_correction_request/list')
            ->with('success', '申請を承認しました。');
    }
}
