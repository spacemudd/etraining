<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('course_batch_session_id');
            $table->foreign('course_batch_session_id')->references('id')->on('course_batch_sessions');
            $table->boolean('is_ready_for_review')->nullable();
            $table->tinyInteger('status');
            $table->uuid('submitted_by')->nullable();
            $table->foreign('submitted_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_reports');
    }
}
