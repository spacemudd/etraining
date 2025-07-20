<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineeCustomCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainee_custom_certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees')->onDelete('cascade');
            $table->string('title');
            $table->timestamp('issued_at')->nullable();
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
        Schema::dropIfExists('trainee_custom_certificates');
    }
}
