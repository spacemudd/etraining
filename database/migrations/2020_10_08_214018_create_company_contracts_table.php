<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('number')->nullable();
            $table->date('date');
            $table->integer('trainees_count')->nullable();
            $table->integer('trainee_salary')->nullable();
            $table->integer('trainer_cost')->nullable();
            $table->integer('company_reimbursement')->nullable();
            $table->string('notes', 999)->nullable();
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
        Schema::dropIfExists('company_contracts');
    }
}
