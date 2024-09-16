<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineeAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainee_agreements', function (Blueprint $table) {
            $table->id();
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees');
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->timestamp('otp')->nullable();
            $table->string('verified_mobile_number')->nullable();
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
        Schema::dropIfExists('trainee_agreements');
    }
}
