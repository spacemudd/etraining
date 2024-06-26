<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineeCompanyMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainee_company_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees');
            $table->string('trainee_name')->nullable();
            $table->string('trainee_identity_number')->nullable();
            $table->string('trainee_phone_number')->nullable();
            $table->timestamp('in_date')->nullable();
            $table->timestamp('out_date')->nullable();
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
        Schema::dropIfExists('trainee_company_movements');
    }
}
