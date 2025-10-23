<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            // إضافة القيمة الجديدة للـ enum باستخدام raw SQL
            DB::statement("ALTER TABLE company_attendance_reports MODIFY COLUMN template_type ENUM('default', 'simple', 'modern', 'gradient') DEFAULT 'default'");
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
