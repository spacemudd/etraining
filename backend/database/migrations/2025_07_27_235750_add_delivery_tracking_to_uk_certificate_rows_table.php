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
        Schema::table('uk_certificate_rows', function (Blueprint $table) {
            $table->string('mailgun_message_id')->nullable()->after('error_message');
            $table->timestamp('delivered_at')->nullable()->after('mailgun_message_id');
            $table->timestamp('failed_at')->nullable()->after('delivered_at');
            $table->text('delivery_failure_reason')->nullable()->after('failed_at');
            $table->string('delivery_status')->default('pending')->after('delivery_failure_reason'); // pending, delivered, failed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uk_certificate_rows', function (Blueprint $table) {
            $table->dropColumn([
                'mailgun_message_id',
                'delivered_at',
                'failed_at',
                'delivery_failure_reason',
                'delivery_status'
            ]);
        });
    }
};
