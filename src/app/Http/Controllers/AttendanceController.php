<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // 今日の日付
        // attendance を作成 or 更新
        // status = working
        // 一覧 or 打刻画面へ戻す
    }

    // 休憩
    public function break()
    {
        // 最新の attendance を取得
        // break_times にレコード追加
        // 戻る
    }

    // 退勤
    public function end()
    {
        // attendance の end_time を入れる
        // status = finished
        // 一覧へ
    }

    // 勤怠一覧
    public function list()
    {
        // 自分の attendances を取得
        $attendances = [
            [
                'id' => 1,
                'date' => '2025-12-30',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'status' => 'finished',
            ],
            [
                'id' => 2,
                'date' => '2025-12-29',
                'start_time' => '09:30',
                'end_time' => '18:30',
                'status' => 'finished',
            ],
        ];// 仮の勤怠一覧データ
        return view('attendance.list',compact('attendances'));
    }

    // 勤怠詳細
    public function detail($id)
    {
        // attendance + break_times を取得
        $attendance = [
            'id' => $id,
            'date' => '2025-12-31',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'status' => 'working',
        ];// 仮の「勤怠1件」
        $breakTimes = [
            ['start_time' => '12:00', 'end_time' => '12:45'],
            ['start_time' => '15:30', 'end_time' => '15:40'],
        ];// 仮の「休憩（複数）」
        return view('attendance.detail',compact('attendance','breakTimes'));
    }
}
