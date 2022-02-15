<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTraineeBankPaymentReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainee_bank_payment_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees');
            $table->bigInteger('amount');
            $table->string('sender_name');
            $table->string('bank_from');
            $table->string('bank_to');
            $table->uuid('uploaded_by_id');
            $table->foreign('uploaded_by_id')->references('id')->on('users');
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
        Schema::dropIfExists('trainee_bank_payment_receipts');
    }
}
