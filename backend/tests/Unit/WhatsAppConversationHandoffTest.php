<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppTag;
use App\Support\WhatsAppConversationHandoff;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Tests\CreatesApplication;

class WhatsAppConversationHandoffTest extends BaseTestCase
{
    use CreatesApplication;

    private string $phone = '+966511111111';

    protected function setUp(): void
    {
        parent::setUp();

        config(['broadcasting.default' => 'null']);
        config(['telnyx.whatsapp_from' => '+966500000001']);

        $this->createMinimalSchema();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('whatsapp_conversation_agents');
        Schema::dropIfExists('whatsapp_conversation_tag');
        Schema::dropIfExists('whatsapp_tags');
        Schema::dropIfExists('whatsapp_bot_sessions');
        Schema::dropIfExists('whatsapp_conversations');

        parent::tearDown();
    }

    public function test_request_human_agent_tags_and_pauses_bot(): void
    {
        $result = WhatsAppConversationHandoff::requestHumanAgent($this->phone, 'out_of_scope');

        $this->assertTrue($result['ok']);
        $this->assertSame('need_human_agent', $result['tag']);

        $tag = WhatsAppTag::query()->where('name', 'need_human_agent')->first();
        $this->assertNotNull($tag);
        $this->assertSame('#DC2626', $tag->color);

        $conversation = WhatsAppConversation::query()->where('phone', $this->phone)->first();
        $this->assertNotNull($conversation);
        $this->assertSame(WhatsAppConversation::STATUS_OPEN, $conversation->status);
        $this->assertNotNull($conversation->bot_paused_until);
        $this->assertTrue($conversation->bot_paused_until->isFuture());
        $this->assertTrue($conversation->tags()->where('whatsapp_tags.id', $tag->id)->exists());
    }

    public function test_request_human_agent_is_idempotent_for_tag(): void
    {
        WhatsAppConversationHandoff::requestHumanAgent($this->phone);
        WhatsAppConversationHandoff::requestHumanAgent($this->phone);

        $this->assertSame(1, WhatsAppTag::query()->where('name', 'need_human_agent')->count());

        $conversation = WhatsAppConversation::query()->where('phone', $this->phone)->first();
        $this->assertSame(1, $conversation->tags()->count());
    }

    private function createMinimalSchema(): void
    {
        Schema::dropIfExists('whatsapp_conversation_agents');
        Schema::dropIfExists('whatsapp_conversation_tag');
        Schema::dropIfExists('whatsapp_tags');
        Schema::dropIfExists('whatsapp_bot_sessions');
        Schema::dropIfExists('whatsapp_conversations');

        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone', 30)->unique();
            $table->uuid('trainee_id')->nullable();
            $table->string('status', 20)->default('open');
            $table->timestamp('bot_paused_until')->nullable();
            $table->text('last_message_body')->nullable();
            $table->string('last_message_direction', 20)->nullable();
            $table->boolean('last_message_is_note')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_bot_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone', 30);
            $table->string('sender_phone', 30);
            $table->uuid('workflow_id')->nullable();
            $table->string('current_node_id')->nullable();
            $table->json('context')->nullable();
            $table->boolean('restart_pending')->default(false);
            $table->timestamps();
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
        });

        Schema::create('whatsapp_conversation_agents', function (Blueprint $table) {
            $table->uuid('conversation_id');
            $table->uuid('user_id');
            $table->timestamp('assigned_at')->nullable();
            $table->primary(['conversation_id', 'user_id']);
        });
    }
}
