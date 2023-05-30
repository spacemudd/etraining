<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_email', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('created_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('number');
            $table->string('applicant');
            $table->string('personal_email');
            $table->integer('phone');
            $table->string('job_title');
            $table->string('manager_name');
            $table->string('manager_email');
            $table->string('new_email');
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
        Schema::dropIfExists('new_email');
    }
}
