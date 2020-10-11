<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->string('reference_number')->nullable();
            $table->string('name');
            $table->string('identity_number')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->uuid('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->string('address')->nullable();
            $table->uuid('marital_status_id')->nullable();
            $table->foreign('marital_status_id')->references('id')->on('marital_statuses');
            $table->uuid('educational_level_id')->nullable();
            $table->foreign('educational_level_id')->references('id')->on('educational_levels');
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
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
        Schema::dropIfExists('trainers');
    }
}
