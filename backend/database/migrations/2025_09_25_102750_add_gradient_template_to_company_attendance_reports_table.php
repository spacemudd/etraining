<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradientTemplateToCompanyAttendanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_attendance_reports', function (Blueprint $table) {
            // تحديث نوع القالب لإضافة الخيار الجديد
            $table->enum('template_type', ['default', 'simple', 'modern', 'gradient'])->default('default')->change();
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
            // إرجاع نوع القالب إلى الحالة السابقة
            $table->enum('template_type', ['default', 'simple', 'modern'])->default('default')->change();
        });
    }
}
