<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUploadCountToAttendanceReportRecordAbsenceNotesTable extends Migration
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
            $table->integer('upload_count')->default(0)->after('rejected_reason');

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
            $table->dropColumn('upload_count');
        });
    }
}
