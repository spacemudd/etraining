<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectedReasonToAttendanceReportRecordAbsenceNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_report_record_absence_notes', function (Blueprint $table) {
            //
            $table->text('rejected_reason')->nullable()->after('rejected_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_report_record_absence_notes', function (Blueprint $table) {
            //
            $table->dropColumn('rejected_reason');

        });
    }
}
