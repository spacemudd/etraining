<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Jobs\PersistWhatsAppInboundMedia;
use App\Jobs\ProcessWhatsAppBotReply;
use App\Jobs\SendWhatsAppChatPushNotification;
use App\Models\Back\WhatsAppMessage;
use App\Services\WhatsAppInboundMediaPersister;
use App\Support\WhatsAppBroadcast;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\CreatesApplication;

class WhatsAppInboundMediaPersisterTest extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        config(['broadcasting.default' => 'null']);
        config(['telnyx.api_key' => 'test-telnyx-key']);
        config(['telnyx.whatsapp_from' => '+966500000000']);
        config(['twilio.account_sid' => 'ACtest']);
        config(['twilio.auth_token' => 'token']);

        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('media');

        $this->createMinimalSchema();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('media');
        parent::tearDown();
    }

    public function test_inbound_media_dispatches_persist_job(): void
    {
        Bus::fake();

        $message = new WhatsAppMessage();
        $message->id = (string) Str::uuid();
        $message->phone = '+966511111111';
        $message->direction = WhatsAppMessage::DIRECTION_INBOUND;
        $message->is_note = false;
        $message->metadata = [
            'media' => [
                ['url' => 'https://media.telnyx.com/abc/photo.jpg', 'content_type' => 'image/jpeg'],
            ],
        ];

        WhatsAppBroadcast::messageStored($message);

        Bus::assertDispatched(PersistWhatsAppInboundMedia::class, function (PersistWhatsAppInboundMedia $job) use ($message) {
            return $job->messageId === $message->id;
        });
        Bus::assertDispatched(ProcessWhatsAppBotReply::class);
        Bus::assertDispatched(SendWhatsAppChatPushNotification::class);
    }

    public function test_text_only_inbound_does_not_dispatch_persist_job(): void
    {
        Bus::fake();

        $message = new WhatsAppMessage();
        $message->id = (string) Str::uuid();
        $message->phone = '+966511111111';
        $message->direction = WhatsAppMessage::DIRECTION_INBOUND;
        $message->is_note = false;
        $message->metadata = [];

        WhatsAppBroadcast::messageStored($message);

        Bus::assertNotDispatched(PersistWhatsAppInboundMedia::class);
    }

    public function test_downloads_telnyx_media_url(): void
    {
        Http::fake([
            'https://media.telnyx.com/*' => Http::response('jpeg-bytes', 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $message = new WhatsAppMessage();
        $message->to_address = '+966500000000';

        $downloaded = (new WhatsAppInboundMediaPersister())->download($message, [
            'url' => 'https://media.telnyx.com/abc/photo.jpg',
            'content_type' => 'image/jpeg',
        ]);

        $this->assertNotNull($downloaded);
        $this->assertSame('jpeg-bytes', $downloaded['contents']);
        $this->assertSame('image/jpeg', $downloaded['mime']);
        $this->assertStringEndsWith('.jpg', $downloaded['filename']);
    }

    public function test_downloads_whatsapp_media_by_id_from_telnyx(): void
    {
        Http::fake([
            'https://api.telnyx.com/v2/whatsapp/media/*' => Http::response('webp-bytes', 200, [
                'Content-Type' => 'image/webp',
            ]),
        ]);

        $message = new WhatsAppMessage();
        $message->to_address = '+966500000000';

        $downloaded = (new WhatsAppInboundMediaPersister())->download($message, [
            'id' => '1330663939258924',
            'kind' => 'sticker',
            'content_type' => 'image/webp',
        ]);

        $this->assertNotNull($downloaded);
        $this->assertSame('webp-bytes', $downloaded['contents']);
        $this->assertSame('image/webp', $downloaded['mime']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/whatsapp/media/')
                && str_contains($request->url(), '1330663939258924')
                && $request->hasHeader('Authorization', 'Bearer test-telnyx-key');
        });
    }

    public function test_twilio_media_urls_use_basic_auth(): void
    {
        Http::fake([
            'https://api.twilio.com/*' => Http::response('pdf-bytes', 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="receipt.pdf"',
            ]),
        ]);

        $message = new WhatsAppMessage();
        $downloaded = (new WhatsAppInboundMediaPersister())->download($message, [
            'url' => 'https://api.twilio.com/2010-04-01/Accounts/ACtest/Messages/MMxxx/Media/MExxx',
            'content_type' => 'application/pdf',
        ]);

        $this->assertSame('pdf-bytes', $downloaded['contents']);
        $this->assertSame('receipt.pdf', $downloaded['filename']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'twilio.com')
                && $request->hasHeader('Authorization');
        });
    }

    public function test_with_persisted_media_rewrites_metadata_urls(): void
    {
        $message = $this->getMockBuilder(WhatsAppMessage::class)
            ->onlyMethods(['persistedMediaPayload'])
            ->getMock();
        $message->method('persistedMediaPayload')->willReturn([
            [
                'id' => 'media-1',
                'url' => 'https://app.test/back/media/media-1',
                'name' => 'photo.jpg',
                'content_type' => 'image/jpeg',
            ],
        ]);

        $payload = $message->withPersistedMedia([
            'metadata' => [
                'media' => [
                    ['url' => 'https://media.telnyx.com/tmp/photo.jpg', 'content_type' => 'image/jpeg'],
                ],
            ],
        ]);

        $this->assertSame('https://app.test/back/media/media-1', $payload['metadata']['media'][0]['url']);
        $this->assertSame('https://app.test/back/media/media-1', $payload['saved_media'][0]['url']);
    }

    public function test_persisted_media_payload_builds_urls_when_media_exists(): void
    {
        $messageId = (string) Str::uuid();
        $mediaId = (string) Str::uuid();

        $message = new class extends WhatsAppMessage {
            public $fakeMedia;

            public function getMedia(string $collectionName = 'default', $filters = []): \Illuminate\Support\Collection
            {
                return collect([$this->fakeMedia]);
            }
        };
        $message->id = $messageId;
        $message->fakeMedia = (object) [
            'id' => $mediaId,
            'file_name' => 'photo.jpg',
            'mime_type' => 'image/jpeg',
        ];

        $payload = $message->persistedMediaPayload();

        $this->assertCount(1, $payload);
        $this->assertSame($mediaId, $payload[0]['id']);
        $this->assertSame('photo.jpg', $payload[0]['name']);
        $this->assertStringContainsString($messageId, $payload[0]['url']);
        $this->assertStringContainsString($mediaId, $payload[0]['url']);
    }

    public function test_persist_skips_expired_urls_when_fail_on_item_error_is_false(): void
    {
        Http::fake([
            '*' => Http::response('gone', 404),
        ]);

        $message = WhatsAppMessage::query()->create([
            'phone' => '+966511111111',
            'direction' => WhatsAppMessage::DIRECTION_INBOUND,
            'is_note' => false,
            'to_address' => '+966500000000',
            'body' => 'photo',
            'metadata' => [
                'media' => [
                    ['url' => 'https://media.telnyx.com/abc/photo.jpg', 'content_type' => 'image/jpeg'],
                ],
            ],
        ]);

        $stored = (new WhatsAppInboundMediaPersister())->persist($message, false);

        $this->assertSame(0, $stored);
    }

    private function createMinimalSchema(): void
    {
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

        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model_type');
            $table->uuid('model_id');
            $table->uuid('uuid')->nullable();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->text('manipulations');
            $table->text('custom_properties');
            $table->text('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->uuid('team_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
