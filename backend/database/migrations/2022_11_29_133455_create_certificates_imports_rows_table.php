<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesImportsRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates_imports_rows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('certificates_import_id');
            $table->foreign('certificates_import_id')->references('id')->on('certificates_imports');
            $table->uuid('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('trainees')->onDelete('cascade');
            $table->uuid('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->timestamp('sent_at')->nullable();
            $table->string('pdf_path')->nullable();
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
        Schema::dropIfExists('certificates_imports_rows');
    }
}
