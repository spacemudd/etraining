<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_links', function (Blueprint $table) {
            $table->id();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->string('type')->unique();
            $table->string('url');
            $table->uuid('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('SET NULL');
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
        Schema::dropIfExists('survey_links');
    }
}
