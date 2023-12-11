<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSentTriesToResignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resignations', function (Blueprint $table) {
            $table->tinyInteger('sent_tries')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resignations', function (Blueprint $table) {
            $table->dropColumn(['sent_tries']);
        });
    }
}
