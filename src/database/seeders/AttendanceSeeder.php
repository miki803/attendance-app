<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionDetail;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //ユーザーを1人取得
        $users = User::where('is_admin', false)->get();
        // 今月
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();
        foreach ($users as $user) {
            $date = $start->copy();
            while ($date <= $end){
                // 土日は休みにする
                if ($date->isWeekend()) {
                    $date->addDay();
                    continue;
                }
                // ランダム判定
                $rand = rand(1, 100);

                //欠勤（20%）
                if ($rand <= 20) {
                    $date->addDay();
                    continue;
                }
                //未退勤（10%）
                if ($rand <= 30) {
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $date->toDateString(),
                        'start_time' => '09:00',
                        'end_time' => null,
                        'status' => 'working',
                    ]);
                    $date->addDay();
                    continue;
                }
                //通常出勤（70%）
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->toDateString(),
                    'start_time' => '09:00',
                    'end_time' => '18:00',
                    'status' => 'finished',
                ]);
                // 休憩（80%）
                if (rand(1, 100) <= 80) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => '12:00',
                        'end_time' => '13:00',
                    ]);
                }
                // 修正申請
                if (rand(1, 100) <= 20) {
                    $status = rand(0, 1) ? 'pending' : 'approved';
                    $request = AttendanceCorrectionRequest::create([
                        'attendance_id' => $attendance->id,
                        'user_id' => $user->id,
                        'status' => $status,
                    ]);
                    AttendanceCorrectionDetail::create([
                        'request_id' => $request->id,
                        'start_time' => '10:00',
                        'end_time' => '19:00',
                        'note' => '打刻忘れのため修正',
                        'target' => 'attendance',
                    ]);
                }
                $date->addDay();
            }
        }
    }
}
