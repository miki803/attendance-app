<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\BreakTime;

//出勤・休憩・退勤機能
class AttendanceTest extends TestCase
{
    use DatabaseMigrations;

    //出勤
    public function test_user_can_start_attendance()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/attendance/start');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id
        ]);
    }

    //休憩
    public function test_user_can_start_break()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working'
        ]);

        $this->actingAs($user)
            ->post('/attendance/break/start');

        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id
        ]);
    }

    //退勤
    public function test_user_can_end_attendance()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working'
        ]);

        $this->actingAs($user)
            ->post('/attendance/end');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => 'finished'
        ]);
    }
    public function test_user_cannot_start_attendance_twice()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/attendance/start');

        $response = $this->actingAs($user)
            ->post('/attendance/start');

        $response->assertStatus(400);
    }

    public function test_user_cannot_request_invalid_time()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'status' => 'normal',
        ]);

        $response = $this->actingAs($user)
            ->from('/attendance/detail/' . $attendance->id)
            ->post('/stamp_correction_request', [
                'attendance_id' => $attendance->id,
                'date' => now()->toDateString(),
                'start_time' => '20:00',
                'end_time' => '18:00',
                'remark' => 'テスト'
            ]);

        $response->assertSessionHasErrors();
    }

    public function test_user_cannot_request_without_remark()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'status' => 'normal',
        ]);

        $response = $this->actingAs($user)
            ->from('/attendance/detail/' . $attendance->id)
            ->post('/stamp_correction_request', [
                'attendance_id' => $attendance->id,
                'date' => now()->toDateString(),
                'start_time' => '09:00',
                'end_time' => '18:00',
                'remark' => '' // ← 空
            ]);

        $response->assertSessionHasErrors('remark');
    }

}
