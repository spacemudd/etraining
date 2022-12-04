<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates_imports', function (Blueprint $table) {
            $table->id();
            $table->uuid('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->string('status');
            $table->integer('processed_count');
            $table->integer('total_count');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->uuid('imported_by_id');
            $table->foreign('imported_by_id')->references('id')->on('users');
            $table->string('filepath');
            $table->text('failed_rows')->nullable();
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
        Schema::dropIfExists('certificates_imports');
    }
}
