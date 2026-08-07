<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\TelnyxWhatsAppService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class TelnyxWhatsAppTemplateComponentsTest extends TestCase
{
    public function test_build_manage_components_includes_header_body_footer_and_examples(): void
    {
        $components = $this->buildManageComponents([
            'header' => 'Order update',
            'body' => 'Hi {{1}}, order {{2}} shipped.',
            'footer' => 'Thank you',
            'variable_samples' => [
                '1' => 'Sara',
                '2' => 'ORD-1',
            ],
        ]);

        $this->assertCount(3, $components);
        $this->assertSame('HEADER', $components[0]['type']);
        $this->assertSame('TEXT', $components[0]['format']);
        $this->assertSame('Order update', $components[0]['text']);

        $this->assertSame('BODY', $components[1]['type']);
        $this->assertSame('Hi {{1}}, order {{2}} shipped.', $components[1]['text']);
        $this->assertSame([['Sara', 'ORD-1']], $components[1]['example']['body_text']);

        $this->assertSame('FOOTER', $components[2]['type']);
        $this->assertSame('Thank you', $components[2]['text']);
    }

    public function test_build_manage_components_fills_missing_examples(): void
    {
        $components = $this->buildManageComponents([
            'body' => 'Code {{1}} expires soon.',
        ]);

        $this->assertCount(1, $components);
        $this->assertSame([['example1']], $components[0]['example']['body_text']);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<int, array<string, mixed>>
     */
    private function buildManageComponents(array $input): array
    {
        $service = new TelnyxWhatsAppService();
        $method = new ReflectionMethod(TelnyxWhatsAppService::class, 'buildManageComponents');
        $method->setAccessible(true);

        return $method->invoke($service, $input);
    }
}
