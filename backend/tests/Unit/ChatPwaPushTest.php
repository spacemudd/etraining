<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\Back\ChatPushSubscriptionController;
use App\Jobs\ProcessWhatsAppBotReply;
use App\Jobs\SendWhatsAppChatPushNotification;
use App\Models\Back\WhatsAppMessage;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\ChatWebPushService;
use App\Support\WhatsAppBroadcast;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mockery;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\CreatesApplication;

class ChatPwaPushTest extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        config(['broadcasting.default' => 'null']);
        config(['chat_pwa.vapid.public_key' => 'test-public-key']);
        config(['chat_pwa.vapid.private_key' => 'test-private-key']);
        config(['chat_pwa.vapid.subject' => 'mailto:test@example.com']);
        config(['permission.cache.expiration_time' => \DateInterval::createFromDateString('0 seconds')]);

        $this->createMinimalSchema();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('users');

        Mockery::close();
        parent::tearDown();
    }

    public function test_inbound_message_dispatches_push_job(): void
    {
        Bus::fake([
            SendWhatsAppChatPushNotification::class,
            ProcessWhatsAppBotReply::class,
        ]);

        $message = WhatsAppMessage::query()->create([
            'id' => (string) Str::uuid(),
            'phone' => '+966500000001',
            'body' => 'Hello agent',
            'direction' => WhatsAppMessage::DIRECTION_INBOUND,
            'status' => 'received',
            'is_note' => false,
            'metadata' => [],
        ]);

        WhatsAppBroadcast::messageStored($message);

        Bus::assertDispatched(SendWhatsAppChatPushNotification::class, function (SendWhatsAppChatPushNotification $job) use ($message) {
            return $job->messageId === $message->id;
        });
    }

    public function test_outbound_message_does_not_dispatch_push_job(): void
    {
        Bus::fake([
            SendWhatsAppChatPushNotification::class,
            ProcessWhatsAppBotReply::class,
        ]);

        $message = WhatsAppMessage::query()->create([
            'id' => (string) Str::uuid(),
            'phone' => '+966500000001',
            'body' => 'Outbound',
            'direction' => WhatsAppMessage::DIRECTION_OUTBOUND,
            'status' => 'sent',
            'is_note' => false,
            'metadata' => [],
        ]);

        WhatsAppBroadcast::messageStored($message);

        Bus::assertNotDispatched(SendWhatsAppChatPushNotification::class);
    }

    public function test_note_does_not_dispatch_push_job(): void
    {
        Bus::fake([
            SendWhatsAppChatPushNotification::class,
            ProcessWhatsAppBotReply::class,
        ]);

        $message = WhatsAppMessage::query()->create([
            'id' => (string) Str::uuid(),
            'phone' => '+966500000001',
            'body' => 'Internal note',
            'direction' => WhatsAppMessage::DIRECTION_INBOUND,
            'status' => 'received',
            'is_note' => true,
            'metadata' => [],
        ]);

        WhatsAppBroadcast::messageStored($message);

        Bus::assertNotDispatched(SendWhatsAppChatPushNotification::class);
    }

    public function test_push_job_sends_to_subscribed_permitted_users_only(): void
    {
        $permission = Permission::create(['name' => 'access-whatsapp-chats', 'guard_name' => 'web']);

        $allowed = User::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'Allowed Agent',
            'email' => 'allowed@example.com',
            'password' => bcrypt('secret'),
        ]);
        $allowed->givePermissionTo($permission);

        $denied = User::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'Denied User',
            'email' => 'denied@example.com',
            'password' => bcrypt('secret'),
        ]);

        $allowedSub = PushSubscription::query()->create([
            'user_id' => $allowed->id,
            'endpoint' => 'https://push.example/allowed',
            'public_key' => 'pk',
            'auth_token' => 'auth',
            'content_encoding' => 'aesgcm',
        ]);

        PushSubscription::query()->create([
            'user_id' => $denied->id,
            'endpoint' => 'https://push.example/denied',
            'public_key' => 'pk',
            'auth_token' => 'auth',
            'content_encoding' => 'aesgcm',
        ]);

        $message = WhatsAppMessage::query()->create([
            'id' => (string) Str::uuid(),
            'phone' => '+966500000001',
            'body' => 'Please help',
            'direction' => WhatsAppMessage::DIRECTION_INBOUND,
            'status' => 'received',
            'is_note' => false,
            'metadata' => [],
        ]);

        $pushService = Mockery::mock(ChatWebPushService::class);
        $pushService->shouldReceive('isConfigured')->andReturn(true);
        $pushService->shouldReceive('sendToSubscription')
            ->once()
            ->withArgs(function (PushSubscription $subscription, array $payload) use ($allowedSub) {
                return $subscription->id === $allowedSub->id
                    && $payload['title'] === 'New WhatsApp message'
                    && $payload['body'] === 'Please help'
                    && $payload['url'] === '/back/chat';
            })
            ->andReturn(true);

        $job = new SendWhatsAppChatPushNotification($message->id);
        $job->handle($pushService);

        $this->addToAssertionCount(1);
    }

    public function test_store_and_destroy_push_subscription(): void
    {
        $permission = Permission::create(['name' => 'access-whatsapp-chats', 'guard_name' => 'web']);
        $user = User::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'Agent',
            'email' => 'agent@example.com',
            'password' => bcrypt('secret'),
        ]);
        $user->givePermissionTo($permission);

        $this->actingAs($user);

        $controller = app(ChatPushSubscriptionController::class);

        $storeRequest = Request::create('/back/chat/push-subscriptions', 'POST', [
            'endpoint' => 'https://push.example/agent-1',
            'public_key' => 'public-key',
            'auth_token' => 'auth-token',
            'content_encoding' => 'aesgcm',
        ]);
        $storeRequest->setUserResolver(fn () => $user);

        $storeResponse = $controller->store($storeRequest);
        $this->assertSame(201, $storeResponse->getStatusCode());
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://push.example/agent-1',
        ]);

        $destroyRequest = Request::create('/back/chat/push-subscriptions', 'DELETE', [
            'endpoint' => 'https://push.example/agent-1',
        ]);
        $destroyRequest->setUserResolver(fn () => $user);

        $destroyResponse = $controller->destroy($destroyRequest);
        $this->assertSame(200, $destroyResponse->getStatusCode());
        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => 'https://push.example/agent-1',
        ]);
    }

    public function test_vapid_public_key_endpoint_payload(): void
    {
        $permission = Permission::create(['name' => 'access-whatsapp-chats', 'guard_name' => 'web']);
        $user = User::query()->create([
            'id' => (string) Str::uuid(),
            'name' => 'Agent',
            'email' => 'agent2@example.com',
            'password' => bcrypt('secret'),
        ]);
        $user->givePermissionTo($permission);

        $controller = app(ChatPushSubscriptionController::class);
        $response = $controller->vapidPublicKey();

        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true);
        $this->assertTrue($data['configured']);
        $this->assertSame('test-public-key', $data['public_key']);
    }

    private function createMinimalSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
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

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('endpoint', 500)->unique();
            $table->string('public_key', 255)->nullable();
            $table->string('auth_token', 255)->nullable();
            $table->string('content_encoding', 32)->nullable();
            $table->timestamps();
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
    }
}
