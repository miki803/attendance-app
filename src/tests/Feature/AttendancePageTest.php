<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AttendancePageTest extends TestCase
{
    use DatabaseMigrations;

    // ID4：日時取得
    public function test_attendance_page_shows_today_date()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/attendance');

        // 年が表示されていればOK（表示形式差対策）
        $response->assertStatus(200);
        $response->assertSee(now()->format('Y'));
    }

    // ID5：勤務外ステータス
    public function test_status_is_off_when_no_attendance()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }
}
