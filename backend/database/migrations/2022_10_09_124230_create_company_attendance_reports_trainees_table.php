<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAttendanceReportsTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_attendance_reports_trainees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_attendance_report_id');
            $table->foreign('company_attendance_report_id', 'attendance_report_comp_trainee_fk')->references('id')->on('company_attendance_reports')->cascadeOnDelete();
            $table->uuid('trainee_id');
            $table->foreign('trainee_id', 'attendance_report_trainee_fk')->references('id')->on('trainees');
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('company_attendance_reports_trainees');
    }
}
