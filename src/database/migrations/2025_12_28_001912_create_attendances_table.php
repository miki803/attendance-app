<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
             // 外部キー（users.id）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
             // 勤怠日
            $table->date('date');
            // 出勤・退勤時刻（最初は空）
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            // 勤怠ステータス
            $table->string('status');
            $table->timestamps();
            // 1日1勤怠制約
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
