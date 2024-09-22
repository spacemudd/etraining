<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseBatchSessionAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_batch_session_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('course_batch_session_id');
            $table->foreign('course_batch_session_id')->references('id')->on('course_batch_sessions');
            $table->uuid('course_batch_id');
            $table->foreign('course_batch_id')->references('id')->on('course_batches');
            $table->uuid('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees');
            $table->uuid('trainee_user_id')->nullable();
            $table->foreign('trainee_user_id')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('session_starts_at');
            $table->timestamp('session_ends_at');
            $table->timestamp('attended_at')->nullable();
            $table->boolean('physical_attendance')->default(false);
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
        Schema::dropIfExists('course_batch_session_attendances');
    }
}
