<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    // 一般ユーザー：申請一覧
    public function userList() 
    {
        $userId = Auth::id();

        // 承認待ち
        $pendingRequests = AttendanceCorrectionRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('attendance')
            ->orderByDesc('created_at')
            ->get();
        // 承認済み
        $approveRequests = AttendanceCorrectionRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->with('attendance')
            ->orderByDesc('created_at')
            ->get();
        
        return view('correction.user_list', compact(
            'pendingRequests'
            'approvedRequests'
        ));
    }

    // 一般ユーザー：修正申請送信
    public function store(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id'
        ]);
        $exists =AttendanceCorrectionRequest::where('attendance_id', $request->attendance_id)
            ->where('user-id', auth()->id())
            ->where('status', 'pending')
            ->exists();
        if($exists) {
            return back()->with('error', 'すでに承認待ちの申請があります。');
        }
        AttendanceCorrectionRequest::create([
            'user_id' => auth()->id(),
            'attendance-_id' => $request->attendance_id,
            'status' => 'pending'
        ]);
        return redirect('/stamp_correction_request/list')
            ->with('success', '修正申請を送信しました。');
    }

    // 申請詳細（一般・管理者共通）
    public function show(id)
    {
        $correction = Attendance
            ->findOrFail($id);
        // 一般ユーザーは自分の申請しか見られない
        if(!auth()->user()->is_admin && $correction->user_id !== auth()->id()) {
            abort(403);
        }

        return view('correction.show', compact('correction'));
    }

    // 管理者：申請一覧
    public function adminList()
    {
        $requests = AttendanceCorrectionRequest::with(['attendance', 'user'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->get();
    }

    // 管理者：申請承認
    public function approve(id)
    {
        $correction =AttendanceCorrectionRequest::findOrFail($id);
        $correction->update([
            'status' => 'approved'
        ]);
        return redirect('/admin/stamp_correction_request/list')
            ->with('success', '申請を承認しました。');
    }
}
