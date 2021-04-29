<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceReportRecordWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_report_record_warnings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('attendance_report_id');
            $table->foreign('attendance_report_id')->references('id')->on('attendance_reports')->cascadeOnDelete();
            $table->uuid('attendance_report_record_id');
            $table->foreign('attendance_report_record_id', 'arr_warnings_arr_id_fk')->references('id')->on('attendance_report_records')->cascadeOnDelete();
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees')->cascadeOnDelete();
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
        Schema::dropIfExists('attendance_report_record_warnings');
    }
}
