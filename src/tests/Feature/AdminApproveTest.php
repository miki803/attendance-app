<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionDetail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

//修正申請承認機能（管理者）
class AdminApproveTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_approve_correction()
    {
        // 管理者作成
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        // 一般ユーザー
        $user = User::factory()->create();

        // 勤怠
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'status' => 'normal',
        ]);

        // 修正申請
        $request = AttendanceCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => 'pending',
        ]);

        // 修正内容
        AttendanceCorrectionDetail::create([
            'request_id' => $request->id,
            'target' => 'attendance',
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
            'note' => 'テスト修正',
        ]);

        // 管理者でログイン
        $this->actingAs($admin);

        // 承認
        $this->post("/admin/stamp_correction_request/approve/{$request->id}");

        // ステータス更新確認
        $this->assertDatabaseHas('attendance_correction_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);

        // 勤怠更新確認
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);
    }
    public function test_admin_cannot_update_when_start_time_after_end_time()
    {
        $admin = User::factory()->create([
            'is_admin' => true
        ]);

        $attendance = Attendance::create([
            'user_id' => User::factory()->create()->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'status' => 'normal',
        ]);

        $response = $this->actingAs($admin)
            ->from('/admin/attendance/' . $attendance->id)
            ->post('/admin/attendance/update', [
                'attendance_id' => $attendance->id,
                'start_time' => '19:00',
                'end_time' => '18:00',
                'remark' => 'テスト'
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }
    public function test_admin_cannot_update_when_remark_empty()
    {
        $admin = User::factory()->create([
            'is_admin' => true
        ]);

        $attendance = Attendance::create([
            'user_id' => User::factory()->create()->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'status' => 'normal',
        ]);

        $response = $this->actingAs($admin)
            ->from('/admin/attendance/' . $attendance->id)
            ->post('/admin/attendance/update', [
                'attendance_id' => $attendance->id,
                'start_time' => '09:00',
                'end_time' => '18:00',
                'remark' => '' // ← 空
            ]);

        $response->assertSessionHasErrors('remark');
    }
}
