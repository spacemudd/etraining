<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyCommsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_comms', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->tinyInteger('type');
            $table->timestamp('occur_at')->nullable();
            $table->boolean('initiated_by_company')->default(false);
            $table->string('description');
            $table->uuid('created_by_id');
            $table->foreign('created_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->tinyInteger('result')->nullable();
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
        Schema::dropIfExists('company_comms');
    }
}
