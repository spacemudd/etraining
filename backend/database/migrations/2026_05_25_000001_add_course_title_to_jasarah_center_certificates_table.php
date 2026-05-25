<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jasarah_center_certificates', function (Blueprint $table) {
            $table->string('course_title')->nullable()->after('course_id');
        });
    }

    public function down(): void
    {
        Schema::table('jasarah_center_certificates', function (Blueprint $table) {
            $table->dropColumn('course_title');
        });
    }
};
