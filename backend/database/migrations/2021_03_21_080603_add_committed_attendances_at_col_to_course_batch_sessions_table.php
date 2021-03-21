<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommittedAttendancesAtColToCourseBatchSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_batch_sessions', function (Blueprint $table) {
            $table->timestamp('committed_attendances_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_batch_sessions', function (Blueprint $table) {
            $table->dropColumn(['committed_attendances_at']);
        });
    }
}
