<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineeWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainee_withdraws', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('number')->index();
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees');
            $table->uuid('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->uuid('approved_by_id')->nullable();
            $table->foreign('approved_by_id')->references('id')->on('users');
            $table->tinyInteger('approved')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainee_withdraws');
    }
}
