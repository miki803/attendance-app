<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AdminAttendanceTest extends TestCase
{
    use DatabaseMigrations;



//勤怠一覧（一般）
    public function test_user_can_view_attendance_list()
    {
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'status' => 'normal',
        ]);

        $response = $this->actingAs($user)
            ->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(now()->format('m/d'));
    }


//勤怠詳細
    public function test_user_can_view_attendance_detail()
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
            ->get('/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('09:00');
    }

//管理者一覧
    public function test_admin_can_view_daily_attendance()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'normal',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

//管理者詳細
    public function test_admin_can_view_attendance_detail()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'normal',
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/attendance/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

// スタッフ一覧
    public function test_admin_can_view_staff_list()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get('/admin/staff/list');

        $response->assertStatus(200);
        $response->assertSee($users[0]->name);
    }

}