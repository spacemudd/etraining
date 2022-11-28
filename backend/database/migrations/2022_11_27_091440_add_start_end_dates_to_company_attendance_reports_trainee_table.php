<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartEndDatesToCompanyAttendanceReportsTraineeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_attendance_reports_trainees', function (Blueprint $table) {
            $table->timestamp('start_date')->nullable()->after('active');
            $table->timestamp('end_date')->nullable()->after('active');
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
            $table->dropColumn(['start_date']);
            $table->dropColumn(['end_date']);
        });
    }
}
