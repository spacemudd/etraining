<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAttendanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_attendance_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('number')->unique();
            $table->timestamp('date_from')->nullable();
            $table->timestamp('date_to')->nullable();
            $table->tinyInteger('status');
            $table->string('to_emails')->nullable();
            $table->string('cc_emails')->nullable();
            $table->uuid('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->uuid('approved_by_id')->nullable();
            $table->foreign('approved_by_id')->references('id')->on('users');
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('company_attendance_reports');
    }
}
