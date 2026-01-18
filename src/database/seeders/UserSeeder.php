<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //管理者ユーザー（1人）
        $param = [
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true, //管理者判定用（Admin画面で使う）
            'email_verified_at' => now(),
        ];
        User::create($param);

        // 一般ユーザー（4人）
        for ($i = 1; $i <= 4; $i++) {
            $param = [
                'name' => "一般ユーザー{$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
                'is_admin' => false,
            ];
            User::create($param);
        }
    }
}
// ※ 全ユーザー共通の仮パスワード: password