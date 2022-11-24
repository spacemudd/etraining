<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCompanyAttendanceReportTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_attendance_reports_trainees', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_attendance_reports_trainees', function (Blueprint $table) {
            $table->dropColumn(['status']);
            $table->dropColumn(['comment']);
        });
    }
}
