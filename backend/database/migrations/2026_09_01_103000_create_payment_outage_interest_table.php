<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_outage_interest', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trainee_id');
            $table->uuid('invoice_id')->nullable();
            $table->timestamp('last_seen_at');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->unique('trainee_id');
            $table->index('notified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_outage_interest');
    }
};
