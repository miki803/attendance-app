<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

//ログイン認証（管理者）
class AdminLoginTest extends TestCase
{
    use DatabaseMigrations;

    public function test_admin_can_login()
    {
        //ログイン機能
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => true
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/attendance/list');
        $this->assertAuthenticatedAs($admin);
    }
    //ログイン--メアドバリデーション
    public function test_admin_can_login_validate_email()
    {
        $response = $this->post('/login', [
            'email' => "",
            'password' => "password",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    //ログイン--パスワードバリデーション
    public function test_admin_can_login_validate_password()
    {
        $response = $this->post('/login', [
            'email' => "admin@test.com",
            'password' => "",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');

        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    //ログイン--不一致
    public function test_admin_can_login_validate_user()
    {
        $response = $this->post('/login', [
            'email' => "admin@test.com",
            'password' => "password123",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertEquals('ログイン情報が登録されていません', $errors->first('email'));
    }
}
