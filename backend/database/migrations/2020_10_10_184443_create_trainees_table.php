<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_additional')->nullable();
            $table->date('birthday')->nullable();
            $table->uuid('educational_level_id')->nullable();
            $table->foreign('educational_level_id')->references('id')->on('educational_levels')->onDelete('SET NULL');
            $table->uuid('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('SET NULL');
            $table->uuid('marital_status_id')->nullable();
            $table->foreign('marital_status_id')->references('id')->on('marital_statuses')->onDelete('SET NULL');
            $table->tinyInteger('children_count')->nullable();
            $table->uuid('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
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
        Schema::dropIfExists('trainees');
    }
}
