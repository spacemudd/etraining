<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWithAttendanceTimesToCompanyAttendanceReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_attendance_reports', function (Blueprint $table) {
            $table->boolean('with_attendance_times')->default(false)->after('cc_emails');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_attendance_reports', function (Blueprint $table) {
            $table->dropColumn(['with_attendance_times']);
        });
    }
}
