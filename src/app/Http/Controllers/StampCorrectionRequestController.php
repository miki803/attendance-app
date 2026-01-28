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
        $attendance = Attendance::where('id', $request->attendance_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $exists =AttendanceCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if($exists) {
            return back()->withErrors([
                'message' => 'すでに承認待ちの申請があります。'
            ]);
        }

        $correction = AttendanceCorrectionRequest::create([
            'user_id' => auth()->id(),
            'attendance_id' => $attendance->id,
            'status' => 'pending',
            'requested_start' => $request->start_time,
            'requested_end'   => $request->end_time,
            'remark' => $request->remark,
        ]);

        foreach ($request->requested_breaks ?? [] as $break) {
            if(!$break['start'] && !$break['end']) continue;

            $correction->breakCorrections()->create([
                'start_time' => $break['start'],
                'end_time' => $break['end'],
            ]);
        }

        return redirect('/stamp_correction_request/list')
            ->with('success', '修正申請を送信しました。');
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
        $status = $request->query('status');
        $query = AttendanceCorrectionRequest::with(['attendance', 'user']);

        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query
            ->orderBy('status')
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
            'breakCorrections'
        ])->findOrFail($id);

        return view('correction.approve', compact('correction'));
    }

    // 管理者：申請承認
    public function approve($id)
    {
        $correction = AttendanceCorrectionRequest::with(['attendance', 'breakCorrections'])
            ->findOrFail($id);

        DB::transaction(function () use ($correction) {
            $attendance = $correction->attendance;

            $attendance->update([
                'start_time' => $correction->requested_start,
                'end_time' => $correction->requested_end,
                'remark' => $correction->remark,
            ]);

            $attendance->breakTimes()->delete();
            foreach ($correction->breakCorrections as $break) {
                $attendance->breakTimes()->create([
                    'start_time' => $break->start_time,
                    'end_time'=> $break->end_time,
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
