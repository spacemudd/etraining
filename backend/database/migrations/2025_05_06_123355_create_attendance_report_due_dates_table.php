<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceReportDueDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_report_due_dates', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable();
            $table->string('filename');
            $table->string('course_name');
            $table->uuid('company_id')->nullable();
            $table->enum('status', ['generating', 'ready'])->default('generating');
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
        Schema::dropIfExists('attendance_report_due_dates');
    }
}
