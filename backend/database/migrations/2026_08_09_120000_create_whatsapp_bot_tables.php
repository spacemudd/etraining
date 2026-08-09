<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_bot_workflows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->json('graph')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_bot_senders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone', 30)->unique();
            $table->string('label')->nullable();
            $table->uuid('workflow_id')->nullable();
            $table->timestamps();

            $table->foreign('workflow_id')
                ->references('id')
                ->on('whatsapp_bot_workflows')
                ->nullOnDelete();

            // One workflow may only be assigned to one sender at a time.
            $table->unique('workflow_id');
        });

        Schema::create('whatsapp_bot_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone', 30);
            $table->string('sender_phone', 30);
            $table->uuid('workflow_id');
            $table->string('current_node_id')->nullable();
            $table->json('context')->nullable();
            $table->boolean('restart_pending')->default(false);
            $table->timestamps();

            $table->unique(['phone', 'sender_phone']);
            $table->foreign('workflow_id')
                ->references('id')
                ->on('whatsapp_bot_workflows')
                ->cascadeOnDelete();
            $table->index('phone');
        });

        if (Schema::hasTable('whatsapp_conversations')) {
            Schema::table('whatsapp_conversations', function (Blueprint $table) {
                $table->timestamp('bot_paused_until')->nullable()->after('status');
            });
        }

        $this->seedDefaultSender();
    }

    public function down(): void
    {
        if (Schema::hasTable('whatsapp_conversations') && Schema::hasColumn('whatsapp_conversations', 'bot_paused_until')) {
            Schema::table('whatsapp_conversations', function (Blueprint $table) {
                $table->dropColumn('bot_paused_until');
            });
        }

        Schema::dropIfExists('whatsapp_bot_sessions');
        Schema::dropIfExists('whatsapp_bot_senders');
        Schema::dropIfExists('whatsapp_bot_workflows');
    }

    private function seedDefaultSender(): void
    {
        $from = (string) (config('telnyx.whatsapp_from') ?: config('twilio.whatsapp_from') ?: '');
        $digits = preg_replace('/\D+/', '', $from) ?? '';

        if ($digits === '') {
            return;
        }

        $exists = DB::table('whatsapp_bot_senders')->where('phone', $digits)->exists();
        if ($exists) {
            return;
        }

        $now = now();

        DB::table('whatsapp_bot_senders')->insert([
            'id' => (string) Str::uuid(),
            'phone' => $digits,
            'label' => 'Primary WhatsApp',
            'workflow_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
};
