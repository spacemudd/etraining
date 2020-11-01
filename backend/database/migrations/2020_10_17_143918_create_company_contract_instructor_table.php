<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyContractInstructorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_contract_instructor', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_contract_id');
            $table->foreign('company_contract_id')->references('id')->on('company_contracts')->cascadeOnDelete();
            $table->uuid('instructor_id');
            $table->foreign('instructor_id')->references('id')->on('instructors')->cascadeOnDelete();
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
        Schema::dropIfExists('company_contract_instructor');
    }
}
