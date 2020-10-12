<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->string('reference_number')->nullable();
            $table->string('name');
            $table->string('identity_number')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('twitter_link')->nullable()->unique();
            $table->uuid('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->uuid('company_contract_id')->nullable();
            $table->foreign('company_contract_id')->references('id')->on('company_contracts')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('company_contracts', function (Blueprint $table) {
            $table->uuid('instructor_id')->nullable();
            $table->foreign('instructor_id')->references('id')->on('instructors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::table('company_contracts', function (Blueprint $table) {
        //    $table->dropForeign(['instructor_id']);
        //    $table->dropColumn(['instructor_id']);
        //});

        Schema::dropIfExists('instructors');
    }
}
