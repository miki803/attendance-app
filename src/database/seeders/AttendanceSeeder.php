<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
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
        //ユーザーを1人取得（なければ作る）
        //「勤怠データが1件もないと画面確認できないから、テスト用に attendance を1件 DB に入れる」
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
                if (rand(1, 100) <= 80) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => '12:00',
                        'end_time' => '13:00',
                    ]);
                }
                $date->addDay();
            }
        }
    }
}
