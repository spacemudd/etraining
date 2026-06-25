<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\IdentityNumberNormalizer;
use PHPUnit\Framework\TestCase;

class IdentityNumberNormalizerTest extends TestCase
{
    public function test_normalizes_arabic_indic_digits(): void
    {
        $this->assertSame('1057559633', IdentityNumberNormalizer::normalize('١٠٥٧٥٥٩٦٣٣'));
    }

    public function test_normalizes_extended_arabic_indic_digits(): void
    {
        $this->assertSame('1057559633', IdentityNumberNormalizer::normalize('۱۰۵۷۵۵۹۶۳۳'));
    }

    public function test_normalizes_mixed_digits_and_strips_separators(): void
    {
        $this->assertSame('1057559633', IdentityNumberNormalizer::normalize(" ١٠٥-٧٥٥٩٦٣٣ \u{200E}"));
    }

    public function test_storage_variants_include_all_digit_systems(): void
    {
        $variants = IdentityNumberNormalizer::storageVariants('1057559633');

        $this->assertContains('1057559633', $variants);
        $this->assertContains('١٠٥٧٥٥٩٦٣٣', $variants);
        $this->assertContains('۱۰۵۷۵۵۹۶۳۳', $variants);
    }
}
