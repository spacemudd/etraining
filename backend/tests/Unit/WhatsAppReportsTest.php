<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\Back\WhatsAppReportsController;
use App\Models\Back\WhatsAppConversation;
use App\Models\Back\WhatsAppMessage;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\CreatesApplication;

class WhatsAppReportsTest extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        config(['permission.cache.expiration_time' => \DateInterval::createFromDateString('0 seconds')]);
        $this->createMinimalSchema();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('whatsapp_conversation_tag');
        Schema::dropIfExists('whatsapp_tags');
        Schema::dropIfExists('whatsapp_conversation_agents');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_view_whatsapp_reports_permission_gates_access(): void
    {
        $permission = Permission::create(['name' => 'view-whatsapp-reports', 'guard_name' => 'web']);

        $denied = User::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'No Access',
            'email' => 'no-access@example.com',
            'password' => bcrypt('secret'),
        ]);

        $allowed = User::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => bcrypt('secret'),
        ]);
        $allowed->givePermissionTo($permission);

        $this->assertFalse($denied->can('view-whatsapp-reports'));
        $this->assertTrue($allowed->can('view-whatsapp-reports'));
    }

    public function test_reports_payload_includes_queue_activity_chase_and_agents(): void
    {
        $agent = User::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'Chaser Agent',
            'email' => 'chaser-agent@example.com',
            'password' => bcrypt('secret'),
        ]);

        $conversation = WhatsAppConversation::query()->create([
            'phone' => '966512345678',
            'status' => WhatsAppConversation::STATUS_OPEN,
            'has_unread' => true,
        ]);
        $conversation->agents()->attach($agent->id, ['assigned_at' => now()]);

        WhatsAppMessage::query()->create([
            'id' => (string) Str::uuid(),
            'phone' => '966512345678',
            'direction' => WhatsAppMessage::DIRECTION_INBOUND,
            'is_note' => false,
            'body' => 'Hello',
            'status' => 'received',
            'sent_at' => now()->subMinutes(30),
            'metadata' => [],
        ]);

        WhatsAppMessage::query()->create([
            'id' => (string) Str::uuid(),
            'phone' => '966512345678',
            'user_id' => $agent->id,
            'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
            'is_note' => false,
            'body' => 'Please pay',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(20),
            'metadata' => [],
        ]);

        WhatsAppMessage::query()->create([
            'id' => (string) Str::uuid(),
            'phone' => '966512345678',
            'user_id' => $agent->id,
            'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
            'is_note' => true,
            'body' => 'Internal note',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(15),
            'metadata' => [],
        ]);

        DB::table('invoices')->insert([
            'id' => (string) Str::uuid(),
            'number' => 'TEST-INV-1',
            'from_date' => now()->startOfMonth()->toDateString(),
            'to_date' => now()->endOfMonth()->toDateString(),
            'sub_total' => 100,
            'tax' => 15,
            'grand_total' => 115,
            'status' => 0,
            'chased_at' => now()->subDay()->toDateTimeString(),
            'chased_by_id' => $agent->id,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        $props = $this->reportProps([
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $this->assertSame(1, $props['queue']['open']);
        $this->assertSame(1, $props['queue']['unread']);
        $this->assertSame(0, $props['queue']['unassigned']);

        $this->assertSame(1, $props['activity']['inbound']);
        $this->assertSame(1, $props['activity']['outbound']);
        $this->assertSame(1, $props['activity']['new_conversations']);

        $this->assertSame(1, $props['chase']['invoices_chased']);
        $this->assertSame(1, $props['chase']['active_chasers']);

        $row = collect($props['agents'])->firstWhere('id', $agent->id);
        $this->assertNotNull($row);
        $this->assertSame('Chaser Agent', $row['name']);
        $this->assertSame(1, $row['assigned']);
        $this->assertSame(1, $row['open_pending']);
        $this->assertSame(1, $row['outbound_messages']);
        $this->assertSame(1, $row['notes']);
        $this->assertSame(1, $row['invoices_chased']);
        $this->assertNotNull($row['avg_first_response_minutes']);
    }

    public function test_date_filter_excludes_older_activity(): void
    {
        $agent = User::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'Old Activity Agent',
            'email' => 'old-activity@example.com',
            'password' => bcrypt('secret'),
        ]);

        $conversation = new WhatsAppConversation();
        $conversation->forceFill([
            'id' => (string) Str::uuid(),
            'phone' => '966598765432',
            'status' => WhatsAppConversation::STATUS_OPEN,
            'has_unread' => false,
            'created_at' => now()->subDays(60),
            'updated_at' => now()->subDays(60),
        ]);
        $conversation->save();

        WhatsAppMessage::query()->create([
            'id' => (string) Str::uuid(),
            'phone' => '966598765432',
            'user_id' => $agent->id,
            'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
            'is_note' => false,
            'body' => 'Old message',
            'status' => 'sent',
            'sent_at' => now()->subDays(45),
            'metadata' => [],
        ]);

        $props = $this->reportProps([
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ]);

        $this->assertSame(0, $props['activity']['outbound']);
        $this->assertSame(0, $props['activity']['new_conversations']);
        $this->assertNull(collect($props['agents'])->firstWhere('id', $agent->id));
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, mixed>
     */
    private function reportProps(array $query): array
    {
        $request = Request::create('/back/chat/reports', 'GET', $query);
        $request->headers->set('X-Inertia', 'true');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $response = app(WhatsAppReportsController::class)
            ->index($request)
            ->toResponse($request);

        $payload = json_decode($response->getContent(), true);

        $this->assertIsArray($payload);
        $this->assertSame('Back/Chat/Reports', $payload['component']);

        return $payload['props'];
    }

    private function createMinimalSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->uuid('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->uuid('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        Schema::create('trainees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
            $table->timestamp('last_inbound_at')->nullable();
            $table->boolean('has_unread')->default(false);
            $table->timestamps();
        });

        Schema::create('whatsapp_conversation_agents', function (Blueprint $table) {
            $table->uuid('conversation_id');
            $table->uuid('user_id');
            $table->timestamp('assigned_at')->nullable();
            $table->primary(['conversation_id', 'user_id']);
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

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone')->nullable();
            $table->text('body')->nullable();
            $table->string('direction')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_note')->default(false);
            $table->json('metadata')->nullable();
            $table->string('twilio_sid')->nullable();
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->uuid('trainee_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('trainee_id')->nullable();
            $table->uuid('company_id')->nullable();
            $table->uuid('created_by_id')->nullable();
            $table->string('number');
            $table->date('from_date');
            $table->date('to_date');
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->integer('status')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('chased_at')->nullable();
            $table->uuid('chased_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
