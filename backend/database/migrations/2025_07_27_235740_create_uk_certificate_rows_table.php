<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uk_certificate_rows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uk_certificate_id');
            $table->char('trainee_id', 36)->nullable();
            $table->string('identity_number');
            $table->string('trainee_name');
            $table->string('filename');
            $table->string('pdf_path')->nullable(); // S3 path
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->foreign('uk_certificate_id')->references('id')->on('uk_certificates')->onDelete('cascade');
            $table->foreign('trainee_id')->references('id')->on('trainees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uk_certificate_rows');
    }
};
