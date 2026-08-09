<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Back\WhatsAppBotSender;
use App\Models\Back\WhatsAppBotSession;
use App\Models\Back\WhatsAppBotWorkflow;
use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppMessage;
use App\Services\WhatsAppBotEngine;
use App\Support\WhatsAppBotPause;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Tests\CreatesApplication;

/**
 * Minimal-schema tests (full migrate:refresh fails on SQLite due to MySQL-only migrations).
 */
class WhatsAppBotEngineTest extends BaseTestCase
{
    use CreatesApplication;

    private string $senderPhone = '+966500000001';

    private string $customerPhone = '+966511111111';

    protected function setUp(): void
    {
        parent::setUp();

        config(['broadcasting.default' => 'null']);
        config(['telnyx.whatsapp_from' => $this->senderPhone]);
        config(['telnyx.api_key' => null]);
        config(['whatsapp.bot_pause_minutes' => 30]);

        $this->createMinimalSchema();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('whatsapp_bot_sessions');
        Schema::dropIfExists('whatsapp_bot_senders');
        Schema::dropIfExists('whatsapp_bot_workflows');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('whatsapp_messages');

        parent::tearDown();
    }

    public function test_bot_replies_when_sender_has_active_workflow(): void
    {
        $this->createWorkflowAndSender([
            'nodes' => [
                '1' => ['id' => '1', 'type' => 'start', 'data' => []],
                '2' => ['id' => '2', 'type' => 'send_message', 'data' => ['body' => 'Welcome to support']],
                '3' => ['id' => '3', 'type' => 'end', 'data' => []],
            ],
            'connections' => [
                '1' => [['from_output' => 'output_1', 'to_node' => '2']],
                '2' => [['from_output' => 'output_1', 'to_node' => '3']],
            ],
        ]);

        app(WhatsAppBotEngine::class)->handleInbound($this->makeInbound('Hello'));

        $outbound = WhatsAppMessage::query()
            ->where('phone', $this->customerPhone)
            ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
            ->first();

        $this->assertNotNull($outbound);
        $this->assertSame('Welcome to support', $outbound->body);
        $this->assertTrue((bool) ($outbound->metadata['is_bot'] ?? false));
    }

    public function test_bot_skips_when_no_workflow_assigned(): void
    {
        WhatsAppBotSender::query()->create([
            'phone' => $this->senderPhone,
            'label' => 'Primary',
            'workflow_id' => null,
        ]);

        app(WhatsAppBotEngine::class)->handleInbound($this->makeInbound('Hello'));

        $this->assertSame(
            0,
            WhatsAppMessage::query()
                ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
                ->count()
        );
    }

    public function test_agent_pause_skips_bot_until_timeout_then_restarts(): void
    {
        $this->createWorkflowAndSender([
            'nodes' => [
                '1' => ['id' => '1', 'type' => 'start', 'data' => []],
                '2' => ['id' => '2', 'type' => 'send_message', 'data' => ['body' => 'Bot hello']],
                '3' => ['id' => '3', 'type' => 'end', 'data' => []],
            ],
            'connections' => [
                '1' => [['from_output' => 'output_1', 'to_node' => '2']],
                '2' => [['from_output' => 'output_1', 'to_node' => '3']],
            ],
        ]);

        WhatsAppBotPause::pauseForAgent($this->customerPhone, 30);

        $conversation = WhatsAppConversation::query()->where('phone', $this->customerPhone)->first();
        $this->assertNotNull($conversation);
        $this->assertTrue($conversation->bot_paused_until->isFuture());

        app(WhatsAppBotEngine::class)->handleInbound($this->makeInbound('During pause'));
        $this->assertSame(
            0,
            WhatsAppMessage::query()
                ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
                ->count()
        );

        $conversation->bot_paused_until = now()->subMinute();
        $conversation->save();

        WhatsAppBotSession::query()->where('phone', $this->customerPhone)->update([
            'restart_pending' => true,
            'current_node_id' => null,
        ]);

        app(WhatsAppBotEngine::class)->handleInbound($this->makeInbound('After pause'));

        $this->assertSame(
            'Bot hello',
            WhatsAppMessage::query()
                ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
                ->value('body')
        );

        $conversation->refresh();
        $this->assertNull($conversation->bot_paused_until);
    }

    public function test_wait_input_keyword_routing(): void
    {
        $this->createWorkflowAndSender([
            'nodes' => [
                '1' => ['id' => '1', 'type' => 'start', 'data' => []],
                '2' => [
                    'id' => '2',
                    'type' => 'wait_input',
                    'data' => [
                        'body' => 'Reply yes or no',
                        'match' => 'keywords',
                        'keywords' => [
                            ['keyword' => 'yes', 'mode' => 'equals', 'output' => 'output_1'],
                            ['keyword' => 'no', 'mode' => 'equals', 'output' => 'output_2'],
                        ],
                    ],
                ],
                '3' => ['id' => '3', 'type' => 'send_message', 'data' => ['body' => 'You said yes']],
                '4' => ['id' => '4', 'type' => 'send_message', 'data' => ['body' => 'You said no']],
                '5' => ['id' => '5', 'type' => 'end', 'data' => []],
            ],
            'connections' => [
                '1' => [['from_output' => 'output_1', 'to_node' => '2']],
                '2' => [
                    ['from_output' => 'output_1', 'to_node' => '3'],
                    ['from_output' => 'output_2', 'to_node' => '4'],
                ],
                '3' => [['from_output' => 'output_1', 'to_node' => '5']],
                '4' => [['from_output' => 'output_1', 'to_node' => '5']],
            ],
        ]);

        app(WhatsAppBotEngine::class)->handleInbound($this->makeInbound('hi'));

        $this->assertSame(
            'Reply yes or no',
            WhatsAppMessage::query()
                ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
                ->value('body')
        );

        $session = WhatsAppBotSession::query()->where('phone', $this->customerPhone)->first();
        $this->assertSame('2', $session->current_node_id);
        $this->assertTrue((bool) ($session->context['waiting'] ?? false));

        app(WhatsAppBotEngine::class)->handleInbound($this->makeInbound('yes'));

        $bodies = WhatsAppMessage::query()
            ->where('direction', WhatsAppMessage::DIRECTION_OUTBOUND)
            ->pluck('body')
            ->all();

        $this->assertContains('You said yes', $bodies);
        $this->assertNotContains('You said no', $bodies);
    }

    public function test_bot_outbound_does_not_pause_conversation(): void
    {
        $this->createWorkflowAndSender([
            'nodes' => [
                '1' => ['id' => '1', 'type' => 'start', 'data' => []],
                '2' => ['id' => '2', 'type' => 'send_message', 'data' => ['body' => 'Auto reply']],
                '3' => ['id' => '3', 'type' => 'end', 'data' => []],
            ],
            'connections' => [
                '1' => [['from_output' => 'output_1', 'to_node' => '2']],
                '2' => [['from_output' => 'output_1', 'to_node' => '3']],
            ],
        ]);

        app(WhatsAppBotEngine::class)->handleInbound($this->makeInbound('ping'));

        $conversation = WhatsAppConversation::query()->where('phone', $this->customerPhone)->first();
        $this->assertNotNull($conversation);
        $this->assertNull($conversation->bot_paused_until);
    }

    public function test_pause_helper_sets_restart_pending_on_sessions(): void
    {
        $workflow = $this->createWorkflowAndSender([
            'nodes' => [
                '1' => ['id' => '1', 'type' => 'start', 'data' => []],
            ],
            'connections' => [],
        ]);

        WhatsAppBotSession::query()->create([
            'phone' => $this->customerPhone,
            'sender_phone' => $this->senderPhone,
            'workflow_id' => $workflow->id,
            'current_node_id' => '1',
            'context' => ['waiting' => true],
            'restart_pending' => false,
        ]);

        WhatsAppBotPause::pauseForAgent($this->customerPhone, 30);

        $session = WhatsAppBotSession::query()->where('phone', $this->customerPhone)->first();
        $this->assertTrue($session->restart_pending);
        $this->assertNull($session->current_node_id);
    }

    private function createMinimalSchema(): void
    {
        Schema::dropIfExists('whatsapp_bot_sessions');
        Schema::dropIfExists('whatsapp_bot_senders');
        Schema::dropIfExists('whatsapp_bot_workflows');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('whatsapp_messages');

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('twilio_sid')->nullable();
            $table->uuid('trainee_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('phone', 30)->index();
            $table->string('direction', 20);
            $table->boolean('is_note')->default(false);
            $table->text('body')->nullable();
            $table->string('status', 30)->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

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
            $table->uuid('workflow_id')->nullable()->unique();
            $table->timestamps();
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
        });
    }

    /**
     * @param  array{nodes: array<string, array<string, mixed>>, connections: array<string, array<int, array<string, mixed>>>}  $graph
     */
    private function createWorkflowAndSender(array $graph): WhatsAppBotWorkflow
    {
        $workflow = WhatsAppBotWorkflow::query()->create([
            'name' => 'Test Workflow',
            'is_active' => true,
            'graph' => $graph,
        ]);

        WhatsAppBotSender::query()->create([
            'phone' => $this->senderPhone,
            'label' => 'Primary',
            'workflow_id' => $workflow->id,
        ]);

        return $workflow;
    }

    private function makeInbound(string $body): WhatsAppMessage
    {
        return WhatsAppMessage::query()->create([
            'phone' => $this->customerPhone,
            'direction' => WhatsAppMessage::DIRECTION_INBOUND,
            'body' => $body,
            'status' => 'received',
            'from_address' => $this->customerPhone,
            'to_address' => $this->senderPhone,
            'sent_at' => now(),
        ]);
    }
}
