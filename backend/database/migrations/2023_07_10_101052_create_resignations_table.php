<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resignations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->uuid('created_by_id');
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('status');
            $table->timestamp('date');
            $table->string('emails_to');
            $table->string('emails_cc')->nullable();
            $table->string('emails_bcc')->nullable();
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
        Schema::dropIfExists('resignations');
    }
}
