<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;

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
        $user = User::first() ?? User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '18:00',
            'status' => 'finished',
        ]);
    }
}
