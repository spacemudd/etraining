<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartUrlForZoom extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_batch_sessions', function (Blueprint $table) {
            $table->string('start_url')->nullable();
            $table->string('join_url')->nullable();
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
            $table->dropColumn(['start_url']);
            $table->dropColumn(['join_url']);
        });
    }
}
