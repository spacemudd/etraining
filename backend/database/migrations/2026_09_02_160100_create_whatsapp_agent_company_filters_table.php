<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_agent_company_filters', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('company_id');
            $table->timestamps();

            $table->primary(['user_id', 'company_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_agent_company_filters');
    }
};
