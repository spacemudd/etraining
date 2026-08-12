<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\Webhooks\TelnyxWhatsAppController;
use App\Services\TelnyxWebhookValidator;
use App\Services\TelnyxWhatsAppService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Regression for E-TRAINING-20H: Array to string conversion when webhook
 * text/caption fields arrive as arrays instead of strings.
 */
class TelnyxWhatsAppBodyExtractionTest extends TestCase
{
    public function test_nested_text_body_array_is_stringified(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'text' => ['body' => 'Hello from nested text'],
        ]);

        $this->assertSame([], $media);
        $this->assertSame('Hello from nested text', $body);
    }

    public function test_plain_string_text_is_preserved(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'text' => 'Plain string body',
        ]);

        $this->assertSame([], $media);
        $this->assertSame('Plain string body', $body);
    }

    public function test_image_caption_array_is_stringified_without_error(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'image' => [
                'url' => 'https://example.com/image.jpg',
                'mime_type' => 'image/jpeg',
                'caption' => ['body' => 'Photo caption'],
            ],
        ]);

        $this->assertCount(1, $media);
        $this->assertSame('https://example.com/image.jpg', $media[0]['url']);
        $this->assertSame('Photo caption', $body);
    }

    public function test_media_without_caption_falls_back_to_placeholder(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'image' => [
                'url' => 'https://example.com/image.jpg',
            ],
        ]);

        $this->assertCount(1, $media);
        $this->assertSame('[Media Attachment]', $body);
    }

    public function test_template_button_reply_uses_button_text(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'type' => 'button',
            'button' => [
                'payload' => 'talk_to_support',
                'text' => 'التحدث مع خدمة العملاء',
            ],
        ]);

        $this->assertSame([], $media);
        $this->assertSame('التحدث مع خدمة العملاء', $body);
    }

    public function test_interactive_button_reply_uses_title(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button_reply',
                'button_reply' => [
                    'id' => 'talk_to_support',
                    'title' => 'التحدث مع خدمة العملاء',
                ],
            ],
        ]);

        $this->assertSame([], $media);
        $this->assertSame('التحدث مع خدمة العملاء', $body);
    }

    public function test_sticker_with_url_is_extracted_and_labeled(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'type' => 'sticker',
            'sticker' => [
                'url' => 'https://example.com/sticker.webp',
                'mime_type' => 'image/webp',
                'animated' => false,
            ],
        ]);

        $this->assertCount(1, $media);
        $this->assertSame('https://example.com/sticker.webp', $media[0]['url']);
        $this->assertSame('image/webp', $media[0]['content_type']);
        $this->assertSame('sticker', $media[0]['kind']);
        $this->assertFalse($media[0]['animated']);
        $this->assertSame('[Sticker]', $body);
    }

    public function test_sticker_without_url_keeps_media_id_metadata(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'type' => 'sticker',
            'sticker' => [
                'id' => 'media-sticker-123',
                'mime_type' => 'image/webp',
                'animated' => true,
            ],
        ]);

        $this->assertCount(1, $media);
        $this->assertSame('media-sticker-123', $media[0]['id']);
        $this->assertArrayNotHasKey('url', $media[0]);
        $this->assertSame('sticker', $media[0]['kind']);
        $this->assertTrue($media[0]['animated']);
        $this->assertSame('[Sticker]', $body);
    }

    public function test_interactive_list_reply_uses_title(): void
    {
        [$media, $body] = $this->extractMediaAndBody([
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list_reply',
                'list_reply' => [
                    'id' => 'option_1',
                    'title' => 'Option one',
                ],
            ],
        ]);

        $this->assertSame('Option one', $body);
    }

    /**
     * @param  array<string, mixed>  $source
     * @return array{0: array<int, array<string, mixed>>, 1: string}
     */
    private function extractMediaAndBody(array $source): array
    {
        $controller = new TelnyxWhatsAppController(
            $this->createMock(TelnyxWhatsAppService::class),
            $this->createMock(TelnyxWebhookValidator::class),
        );

        $method = new ReflectionMethod(TelnyxWhatsAppController::class, 'extractMediaAndBody');
        $method->setAccessible(true);

        return $method->invoke($controller, $source);
    }
}
