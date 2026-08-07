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
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone', 30)->unique();
            $table->uuid('trainee_id')->nullable();
            $table->foreign('trainee_id')->references('id')->on('trainees')->nullOnDelete();
            $table->text('last_message_body')->nullable();
            $table->string('last_message_direction', 20)->nullable();
            $table->boolean('last_message_is_note')->default(false);
            $table->timestamp('last_message_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('whatsapp_conversation_agents', function (Blueprint $table) {
            $table->uuid('conversation_id');
            $table->uuid('user_id');
            $table->timestamp('assigned_at')->useCurrent();

            $table->primary(['conversation_id', 'user_id']);
            $table->foreign('conversation_id')
                ->references('id')
                ->on('whatsapp_conversations')
                ->cascadeOnDelete();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->index('user_id');
        });

        Schema::create('whatsapp_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('color', 32)->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_conversation_tag', function (Blueprint $table) {
            $table->uuid('conversation_id');
            $table->uuid('tag_id');

            $table->primary(['conversation_id', 'tag_id']);
            $table->foreign('conversation_id')
                ->references('id')
                ->on('whatsapp_conversations')
                ->cascadeOnDelete();
            $table->foreign('tag_id')
                ->references('id')
                ->on('whatsapp_tags')
                ->cascadeOnDelete();
            $table->index('tag_id');
        });

        $this->backfillConversations();
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversation_tag');
        Schema::dropIfExists('whatsapp_tags');
        Schema::dropIfExists('whatsapp_conversation_agents');
        Schema::dropIfExists('whatsapp_conversations');
    }

    private function backfillConversations(): void
    {
        if (! Schema::hasTable('whatsapp_messages')) {
            return;
        }

        $phones = DB::table('whatsapp_messages')
            ->select('phone')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->distinct()
            ->pluck('phone');

        $now = now();

        foreach ($phones as $phone) {
            $lastMessage = DB::table('whatsapp_messages')
                ->where('phone', $phone)
                ->orderByDesc('sent_at')
                ->orderByDesc('created_at')
                ->first();

            if (! $lastMessage) {
                continue;
            }

            $lastMessageAt = $lastMessage->sent_at ?? $lastMessage->created_at ?? $now;

            DB::table('whatsapp_conversations')->insert([
                'id' => (string) Str::uuid(),
                'phone' => $phone,
                'trainee_id' => $lastMessage->trainee_id,
                'last_message_body' => $lastMessage->body,
                'last_message_direction' => $lastMessage->direction,
                'last_message_is_note' => (bool) ($lastMessage->is_note ?? false),
                'last_message_at' => $lastMessageAt,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
