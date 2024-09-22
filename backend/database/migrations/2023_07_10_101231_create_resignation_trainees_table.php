<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResignationTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resignation_trainees', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');

            $table->uuid('resignation_id');
            $table->foreign('resignation_id')->references('id')->on('resignations')->cascadeOnDelete();

            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees');
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
        Schema::dropIfExists('resignation_trainees');
    }
}
