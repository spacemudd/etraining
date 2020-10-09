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
            $table->string('reference_number')->nullable();
            $table->timestamp('contract_starts_at');
            $table->timestamp('contract_ends_at')->nullable();
            $table->tinyInteger('contract_period_in_months')->nullable();
            $table->boolean('auto_renewal')->default(true);
            $table->integer('trainees_count')->nullable();
            $table->integer('trainee_salary')->nullable();
            $table->integer('trainer_cost')->nullable();
            $table->integer('company_reimbursement')->nullable();
            $table->string('notes', 999)->nullable();
            $table->uuid('created_by_id')->nullable();
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('SET NULL');
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
