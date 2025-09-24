<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplateTypeToCompanyAttendanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_attendance_reports', function (Blueprint $table) {
            $table->enum('template_type', ['default', 'simple', 'modern'])->default('default')->after('with_logo');
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
            $table->dropColumn('template_type');
        });
    }
}
