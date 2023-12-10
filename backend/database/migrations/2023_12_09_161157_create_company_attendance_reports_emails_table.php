<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAttendanceReportsEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_attendance_reports_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_attendance_report_id');
            $table->foreign('company_attendance_report_id', 'ca_report_id')->references('id')->on('company_attendance_reports');
            $table->string('email');
            $table->char('type', 3)->comment('to, cc, bcc');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('clicked_confirmed_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failed_reason')->nullable();
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
        Schema::dropIfExists('company_attendance_reports_emails');
    }
}
