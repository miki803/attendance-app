<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceCorrectionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_correction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('attendance_correction_requests')->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->text('note');
            $table->timestamps();
            $table->string('target'); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_correction_requests', function (Blueprint $table) {
            $table->text('remark')->nullable();
        });
    }
}
