<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgressTrackingToJobTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_trackers', function (Blueprint $table) {
            $table->integer('total_records')->nullable()->after('finished_at');
            $table->integer('processed_records')->default(0)->after('total_records');
            $table->decimal('progress_percentage', 5, 2)->default(0)->after('processed_records');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_trackers', function (Blueprint $table) {
            $table->dropColumn(['total_records', 'processed_records', 'progress_percentage']);
        });
    }
}
