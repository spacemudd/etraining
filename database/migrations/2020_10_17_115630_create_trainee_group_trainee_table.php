<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineeGroupTraineeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainee_group_trainee', function (Blueprint $table) {
            $table->id();
            $table->uuid('trainee_group_id');
            $table->foreign('trainee_group_id')->references('id')->on('trainee_groups')->cascadeOnDelete();
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees')->cascadeOnDelete();
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
        Schema::dropIfExists('trainee_group_trainee');
    }
}
