<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineeBlockListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainee_block_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('trainee_id')->nullable();
            $table->foreign('trainee_id')->references('id')->on('trainees')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_additional')->nullable();
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainee_block_lists');
    }
}
