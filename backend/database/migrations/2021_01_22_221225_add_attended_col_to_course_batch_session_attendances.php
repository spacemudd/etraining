<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendedColToCourseBatchSessionAttendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_batch_session_attendances', function (Blueprint $table) {
            $table->boolean('attended')->after('attended_at');
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
            $table->dropColumn(['attended']);
        });
    }
}
