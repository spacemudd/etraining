<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('twilio_sid')->nullable()->unique();
            $table->uuid('trainee_id')->nullable();
            $table->foreign('trainee_id')->references('id')->on('trainees')->nullOnDelete();
            $table->string('phone', 20)->index();
            $table->string('direction', 20);
            $table->text('body')->nullable();
            $table->string('status', 30)->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
