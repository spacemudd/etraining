<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedByIdToTrainees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->uuid('deleted_by_id')->nullable();
            $table->foreign('deleted_by_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainees', function (Blueprint $table) {
            if (env('DB_CONNECTION') != 'sqlite') {
                $table->dropForeign(['deleted_by_id']);
            }
            $table->dropColumn(['deleted_by_id']);
        });
    }
}
