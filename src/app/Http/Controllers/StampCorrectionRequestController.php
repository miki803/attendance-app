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

        $pendingRequests = AttendanceCorrectionRequest::with
    }

    // 一般ユーザー：修正申請送信
    public function store() { }

    // 申請詳細（一般・管理者共通）
    public function show() { }

    // 管理者：申請一覧
    public function adminList() { }

    // 管理者：申請承認
    public function approve() { }
}
