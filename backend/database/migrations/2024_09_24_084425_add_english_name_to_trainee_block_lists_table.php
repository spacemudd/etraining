<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnglishNameToTraineeBlockListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainee_block_lists', function (Blueprint $table) {
            $table->string('english_name')->nullable(); // Add the english_name column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trainee_block_lists', function (Blueprint $table) {
            $table->dropColumn('english_name'); // Drop the english_name column
        });
    }
}
