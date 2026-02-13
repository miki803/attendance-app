<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

//勤怠修正申請機能
class CorrectionRequestTest extends TestCase
{
    use DatabaseMigrations;

    public function test_user_can_send_correction_request()
    {
        $user = User::factory()->create();

        //修正申請
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'status' => 'normal',
        ]);

        $this->actingAs($user);

        $this->post('/stamp_correction_request', [
            'attendance_id' => $attendance->id,
            'date' => now()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '19:00',
            'remark' => '打刻修正',
        ]);

        $this->assertDatabaseHas('attendance_correction_requests', [
            'attendance_id' => $attendance->id,
            'status' => 'pending',
        ]);
    }
}
