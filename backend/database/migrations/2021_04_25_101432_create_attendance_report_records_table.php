<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceReportRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_report_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->uuid('attendance_report_id');
            $table->foreign('attendance_report_id')->references('id')->on('attendance_reports');
            $table->uuid('course_id');
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->uuid('course_batch_id');
            $table->foreign('course_batch_id')->references('id')->on('course_batches')->cascadeOnDelete();
            $table->uuid('course_batch_session_id');
            $table->foreign('course_batch_session_id')->references('id')->on('course_batch_sessions');
            $table->timestamp('session_starts_at');
            $table->timestamp('session_ends_at');
            $table->uuid('instructor_id');
            $table->foreign('instructor_id')->references('id')->on('instructors');
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees');
            $table->uuid('trainee_user_id')->nullable();
            $table->timestamp('attended_at')->nullable();
            $table->tinyInteger('status');
            $table->string('absence_reason')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('attendance_report_records');
    }
}
