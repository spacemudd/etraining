<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappTemplateBindingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_template_bindings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('template_sid')->unique();
            $table->string('template_name')->nullable();
            $table->string('language', 20)->nullable();
            $table->json('bindings');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_template_bindings');
    }
}
