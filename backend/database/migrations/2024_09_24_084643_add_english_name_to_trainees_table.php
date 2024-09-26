<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnglishNameToTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->string('english_name')->nullable(); 
        });

        Schema::table('trainees', function (Blueprint $table) {
            $table->string('english_name')->nullable(); // Add the new column
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
            $table->dropColumn('english_name'); // Drop the english_name column
        });

        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn('english_name');
        });
    }
}
