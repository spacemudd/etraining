<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Jobs\ProcessWhatsAppBotReply;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\Back\WhatsAppBotSender;
use App\Models\Back\WhatsAppBotWorkflow;
use App\Models\Back\WhatsAppMessage;
use App\Services\NoonService;
use App\Services\WhatsAppAiBotService;
use App\Services\WhatsAppAiTraineeTools;
use App\Services\WhatsAppBotEngine;
use App\Support\WhatsAppAiSettings;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mockery;
use Tests\CreatesApplication;

/**
 * Minimal-schema tests (full migrate:refresh fails on SQLite due to MySQL-only migrations).
 */
class WhatsAppAiBotTest extends BaseTestCase
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
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('whatsapp_bot_sessions');
        Schema::dropIfExists('whatsapp_bot_senders');
        Schema::dropIfExists('whatsapp_bot_workflows');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('whatsapp_messages');

        Mockery::close();
        parent::tearDown();
    }

    public function test_api_key_is_masked_in_admin_payload(): void
    {
        WhatsAppAiSettings::save([
            'enabled' => true,
            'openai_key' => 'sk-abcdefghijklmnopqrstuvwxyz',
            'model' => 'gpt-4o-mini',
            'system_prompt' => 'sys',
            'purpose' => 'purpose',
            'tone' => 'tone',
            'handoff_rules' => 'handoff',
            'max_reply_chars' => 800,
        ]);

        $payload = WhatsAppAiSettings::forAdmin();

        $this->assertTrue($payload['openai_key_set']);
        $this->assertSame('sk-…wxyz', $payload['openai_key_masked']);
        $this->assertArrayNotHasKey('openai_key', $payload);
        $this->assertStringNotContainsString('abcdefghijklmnopqrst', json_encode($payload));
    }

    public function test_empty_api_key_keeps_existing(): void
    {
        WhatsAppAiSettings::save([
            'enabled' => false,
            'openai_key' => 'sk-original-secret-key-1234',
            'model' => 'gpt-4o-mini',
            'system_prompt' => 'sys',
            'purpose' => 'purpose',
            'tone' => 'tone',
            'handoff_rules' => 'handoff',
            'max_reply_chars' => 800,
        ]);

        WhatsAppAiSettings::save([
            'enabled' => true,
            'openai_key' => '',
            'model' => 'gpt-4o-mini',
            'system_prompt' => 'sys2',
            'purpose' => 'purpose',
            'tone' => 'tone',
            'handoff_rules' => 'handoff',
            'max_reply_chars' => 500,
        ]);

        $this->assertSame('sk-original-secret-key-1234', WhatsAppAiSettings::getApiKey());
        $this->assertTrue(WhatsAppAiSettings::isEnabled());
        $this->assertSame(500, WhatsAppAiSettings::getMaxReplyChars());
    }

    public function test_tools_return_profile_contract_suspension_and_invoices(): void
    {
        $trainee = $this->createTrainee([
            'phone' => '966511111111',
            'name' => 'Test Trainee',
            'english_name' => 'Test',
            'identity_number' => '1234567890',
            'zoho_contract_status' => 'completed',
            'zoho_contract_id' => 'req-1',
            'zoho_sign_date' => now()->toDateString(),
            'must_sign' => false,
            'suspended_at' => null,
        ]);

        $invoiceId = (string) Str::uuid();
        \DB::table('invoices')->insert([
            'id' => $invoiceId,
            'trainee_id' => $trainee->id,
            'company_id' => $trainee->company_id,
            'number' => 1001,
            'status' => Invoice::STATUS_UNPAID,
            'grand_total' => 1500.50,
            'from_date' => now()->subMonth()->toDateString(),
            'to_date' => now()->toDateString(),
            'paid_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tools = app(WhatsAppAiTraineeTools::class);

        $profile = $tools->getTraineeProfile($this->customerPhone);
        $this->assertTrue($profile['found']);
        $this->assertSame('Test Trainee', $profile['name']);
        $this->assertSame('7890', $profile['identity_last4']);

        $contract = $tools->getContractStatus($this->customerPhone);
        $this->assertTrue($contract['is_signed']);
        $this->assertSame('completed', $contract['zoho_contract_status']);

        $account = $tools->getAccountStatus($this->customerPhone);
        $this->assertFalse($account['is_suspended']);

        $pending = $tools->getPendingInvoices($this->customerPhone);
        $this->assertSame(1, $pending['count']);
        $this->assertSame($invoiceId, $pending['invoices'][0]['id']);
    }

    public function test_tools_detect_suspended_trainee(): void
    {
        $this->createTrainee([
            'phone' => '966511111111',
            'name' => 'Suspended',
            'suspended_at' => now(),
            'deleted_remark' => 'عدم السداد',
            'deleted_at' => now(),
        ]);

        $account = app(WhatsAppAiTraineeTools::class)->getAccountStatus($this->customerPhone);

        $this->assertTrue($account['found']);
        $this->assertTrue($account['is_suspended']);
        $this->assertSame('عدم السداد', $account['reason']);
    }

    public function test_payment_link_rejects_other_trainee_invoice(): void
    {
        $owner = $this->createTrainee([
            'phone' => '966511111111',
            'name' => 'Owner',
        ]);
        $other = $this->createTrainee([
            'phone' => '966522222222',
            'name' => 'Other',
        ]);

        $foreignInvoiceId = (string) Str::uuid();
        \DB::table('invoices')->insert([
            'id' => $foreignInvoiceId,
            'trainee_id' => $other->id,
            'company_id' => $other->company_id,
            'number' => 2002,
            'status' => Invoice::STATUS_UNPAID,
            'grand_total' => 100,
            'from_date' => now()->toDateString(),
            'to_date' => now()->toDateString(),
            'paid_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $result = app(WhatsAppAiTraineeTools::class)->createPaymentLink(
            $this->customerPhone,
            $foreignInvoiceId
        );

        $this->assertFalse($result['ok']);
        $this->assertSame('invoice_not_found_or_not_owned', $result['error']);
    }

    public function test_payment_link_for_owned_invoice(): void
    {
        $trainee = $this->createTrainee([
            'phone' => '966511111111',
            'name' => 'Owner',
        ]);

        $invoiceId = (string) Str::uuid();
        \DB::table('invoices')->insert([
            'id' => $invoiceId,
            'trainee_id' => $trainee->id,
            'company_id' => $trainee->company_id,
            'number' => 3003,
            'status' => Invoice::STATUS_UNPAID,
            'grand_total' => 250,
            'from_date' => now()->toDateString(),
            'to_date' => now()->toDateString(),
            'paid_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $noon = Mockery::mock(NoonService::class);
        $noon->shouldReceive('createPaymentUrlForInvoice')
            ->once()
            ->andReturn('https://pay.example/checkout');
        $this->app->instance(NoonService::class, $noon);

        $result = app(WhatsAppAiTraineeTools::class)->createPaymentLink(
            $this->customerPhone,
            $invoiceId
        );

        $this->assertTrue($result['ok']);
        $this->assertSame('https://pay.example/checkout', $result['payment_url']);
    }

    public function test_job_uses_ai_when_ready_and_skips_drawflow(): void
    {
        $this->createWorkflowAndSender();

        WhatsAppAiSettings::save([
            'enabled' => true,
            'openai_key' => 'sk-test-key-for-routing',
            'model' => 'gpt-4o-mini',
            'system_prompt' => 'sys',
            'purpose' => 'purpose',
            'tone' => 'tone',
            'handoff_rules' => 'handoff',
            'max_reply_chars' => 800,
        ]);

        $inbound = $this->makeInbound('Hello AI');

        $ai = Mockery::mock(WhatsAppAiBotService::class);
        $ai->shouldReceive('handleInbound')->once()->with(Mockery::on(function ($message) use ($inbound) {
            return $message instanceof WhatsAppMessage && $message->id === $inbound->id;
        }));
        $this->app->instance(WhatsAppAiBotService::class, $ai);

        $engine = Mockery::mock(WhatsAppBotEngine::class);
        $engine->shouldNotReceive('handleInbound');
        $this->app->instance(WhatsAppBotEngine::class, $engine);

        $this->assertTrue(WhatsAppAiSettings::isReady());

        (new ProcessWhatsAppBotReply($inbound->id))->handle(
            app(WhatsAppBotEngine::class),
            app(WhatsAppAiBotService::class)
        );

        $this->addToAssertionCount(1);
    }

    public function test_job_uses_drawflow_when_ai_disabled(): void
    {
        WhatsAppAiSettings::save([
            'enabled' => false,
            'openai_key' => 'sk-test-key-for-routing',
            'model' => 'gpt-4o-mini',
            'system_prompt' => 'sys',
            'purpose' => 'purpose',
            'tone' => 'tone',
            'handoff_rules' => 'handoff',
            'max_reply_chars' => 800,
        ]);

        $inbound = $this->makeInbound('Hello flow');

        $ai = Mockery::mock(WhatsAppAiBotService::class);
        $ai->shouldNotReceive('handleInbound');
        $this->app->instance(WhatsAppAiBotService::class, $ai);

        $engine = Mockery::mock(WhatsAppBotEngine::class);
        $engine->shouldReceive('handleInbound')->once();
        $this->app->instance(WhatsAppBotEngine::class, $engine);

        $this->assertFalse(WhatsAppAiSettings::isReady());

        (new ProcessWhatsAppBotReply($inbound->id))->handle(
            app(WhatsAppBotEngine::class),
            app(WhatsAppAiBotService::class)
        );

        $this->addToAssertionCount(1);
    }

    public function test_unknown_phone_profile_not_found(): void
    {
        $profile = app(WhatsAppAiTraineeTools::class)->getTraineeProfile('+966599999999');

        $this->assertTrue($profile['ok']);
        $this->assertFalse($profile['found']);
    }

    private function createMinimalSchema(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('whatsapp_bot_sessions');
        Schema::dropIfExists('whatsapp_bot_senders');
        Schema::dropIfExists('whatsapp_bot_workflows');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('whatsapp_messages');

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_ar')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('trainees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('english_name')->nullable();
            $table->string('email')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('zoho_contract_id')->nullable();
            $table->string('zoho_contract_status')->nullable();
            $table->date('zoho_sign_date')->nullable();
            $table->boolean('must_sign')->default(false);
            $table->timestamp('suspended_at')->nullable();
            $table->string('deleted_remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trainee_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->unsignedBigInteger('number')->nullable();
            $table->integer('status')->default(0);
            $table->decimal('grand_total', 10, 2)->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

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
     * @param  array<string, mixed>  $attributes
     */
    private function createTrainee(array $attributes): Trainee
    {
        $companyId = (string) Str::uuid();
        \DB::table('companies')->insert([
            'id' => $companyId,
            'name_ar' => 'شركة اختبار',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $id = (string) Str::uuid();
        $row = array_merge([
            'id' => $id,
            'company_id' => $companyId,
            'email' => Str::random(8) . '@example.test',
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes);

        \DB::table('trainees')->insert($row);

        return Trainee::withTrashed()->findOrFail($id);
    }

    private function createWorkflowAndSender(): WhatsAppBotWorkflow
    {
        $workflow = WhatsAppBotWorkflow::query()->create([
            'name' => 'Test Workflow',
            'is_active' => true,
            'graph' => [
                'nodes' => [
                    '1' => ['id' => '1', 'type' => 'start', 'data' => []],
                    '2' => ['id' => '2', 'type' => 'send_message', 'data' => ['body' => 'Welcome']],
                    '3' => ['id' => '3', 'type' => 'end', 'data' => []],
                ],
                'connections' => [
                    '1' => [['from_output' => 'output_1', 'to_node' => '2']],
                    '2' => [['from_output' => 'output_1', 'to_node' => '3']],
                ],
            ],
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
