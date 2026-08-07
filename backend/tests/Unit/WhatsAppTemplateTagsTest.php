<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\WhatsAppTemplateTags;
use PHPUnit\Framework\TestCase;

class WhatsAppTemplateTagsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        WhatsAppTemplateTags::setDefinitionsForTesting([
            'trainee_name' => [
                'label' => 'Trainee name',
                'example' => 'أحمد',
                'example_en' => 'Ahmed',
            ],
        ]);
    }

    protected function tearDown(): void
    {
        WhatsAppTemplateTags::setDefinitionsForTesting(null);
        parent::tearDown();
    }

    public function test_normalize_body_converts_named_tags_to_numbers(): void
    {
        $result = WhatsAppTemplateTags::normalizeBody(
            'مرحبا {{trainee_name}}، رقم طلبك {{1}}',
            ['1' => 'ORD-9'],
            'ar'
        );

        $this->assertSame('مرحبا {{1}}، رقم طلبك {{2}}', $result['body']);
        $this->assertSame(['1' => 'trainee_name'], $result['bindings']);
        $this->assertSame('أحمد', $result['samples']['1']);
        $this->assertSame('ORD-9', $result['samples']['2']);
    }

    public function test_apply_bindings_restores_named_tags(): void
    {
        $body = WhatsAppTemplateTags::applyBindingsToBody('Hi {{1}}', ['1' => 'trainee_name']);

        $this->assertSame('Hi {{trainee_name}}', $body);
    }

    public function test_trainee_name_is_auto_tag(): void
    {
        $this->assertTrue(WhatsAppTemplateTags::isAutoTag('trainee_name'));
        $this->assertFalse(WhatsAppTemplateTags::isAutoTag('custom_field'));
    }
}
