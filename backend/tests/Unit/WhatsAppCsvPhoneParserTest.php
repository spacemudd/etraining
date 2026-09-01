<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\WhatsAppCsvPhoneParser;
use PHPUnit\Framework\TestCase;

class WhatsAppCsvPhoneParserTest extends TestCase
{
    public function test_extracts_phone_column_from_outage_csv(): void
    {
        $csv = <<<'CSV'
trainee_id,name,phone,invoice_id,last_seen_at,notified_at
999d952e-ee00-4b2b-bbd6-da07d244da68,حلا خالد,0532041228,,2026-09-01 07:41:51,
6204851a-2cf5-4016-b3d1-ea8059686105,شهره تركي,507700798,,2026-09-01 07:42:12,
CSV;

        $phones = WhatsAppCsvPhoneParser::extractPhones($csv);

        $this->assertSame(['0532041228', '507700798'], $phones);
    }

    public function test_converts_eastern_arabic_digits(): void
    {
        $csv = "phone\n٠٥٥٩٩٨٦٣٩٩\n";

        $phones = WhatsAppCsvPhoneParser::extractPhones($csv);

        $this->assertSame(['0559986399'], $phones);
    }

    public function test_scans_all_cells_when_no_phone_header(): void
    {
        $csv = "حلا خالد,0532041228\nشهره,966500627864\n";

        $phones = WhatsAppCsvPhoneParser::extractPhones($csv);

        $this->assertSame(['0532041228', '966500627864'], $phones);
    }

    public function test_deduplicates_by_digits(): void
    {
        $csv = "phone\n0532041228\n+966532041228\n";

        $phones = WhatsAppCsvPhoneParser::extractPhones($csv);

        $this->assertSame(['0532041228'], $phones);
    }
}
