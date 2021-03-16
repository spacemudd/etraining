<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbsenceReasonToAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_batch_session_attendances', function (Blueprint $table) {
            $table->string('absence_reason')->nullable();
            $table->tinyInteger('status')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_batch_session_attendances', function (Blueprint $table) {
            //
        });
    }
}
