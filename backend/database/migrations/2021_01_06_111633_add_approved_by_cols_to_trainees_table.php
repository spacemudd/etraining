<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedByColsToTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->uuid('approved_by_id')->nullable();
            $table->foreign('approved_by_id')->references('id')->on('users');
            $table->timestamp('approved_at')->nullable();
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
                $table->dropForeign(['approved_by_id']);
            }
            $table->dropColumn(['approved_by_id', 'approved_at']);
        });
    }
}
