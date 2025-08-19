<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleDriveFieldsToUkCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uk_certificates', function (Blueprint $table) {
            $table->string('drive_url')->nullable()->after('course_id');
            $table->decimal('progress_percentage', 5, 2)->default(0)->after('failed_count');
            $table->string('current_file')->nullable()->after('progress_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uk_certificates', function (Blueprint $table) {
            $table->dropColumn(['drive_url', 'progress_percentage', 'current_file']);
        });
    }
}
