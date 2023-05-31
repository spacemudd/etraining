<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_emails', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('created_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('number');
            $table->tinyInteger('status');
            $table->string('applicant');
            $table->string('personal_email');
            $table->integer('phone');
            $table->string('job_title');
            $table->string('manager_name');
            $table->string('manager_email');
            $table->string('new_email');
            $table->string('rejected_reason');
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
        Schema::dropIfExists('new_emails');
    }
}
