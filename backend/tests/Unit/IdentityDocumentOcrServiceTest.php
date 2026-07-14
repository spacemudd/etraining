<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\IdentityDocumentOcrService;
use PHPUnit\Framework\TestCase;

class IdentityDocumentOcrServiceTest extends TestCase
{
    private IdentityDocumentOcrService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new IdentityDocumentOcrService();
    }

    public function test_extracts_english_name_after_name_label(): void
    {
        $text = <<<'TXT'
KINGDOM OF SAUDI ARABIA
MINISTRY OF INTERIOR
NATIONAL ID
الاسم
Name
فهد بن محمد
FAHAD MOHAMMED ALQAHTANI
رقم الهوية
1098765432
TXT;

        $this->assertSame(
            'Fahad Mohammed Alqahtani',
            $this->service->parseEnglishNameFromOcrText($text)
        );
    }

    public function test_extracts_english_name_from_inline_name_label(): void
    {
        $text = <<<'TXT'
Kingdom of Saudi Arabia
Name: SARA AHMED ALHARBI
ID 1122334455
TXT;

        $this->assertSame(
            'Sara Ahmed Alharbi',
            $this->service->parseEnglishNameFromOcrText($text)
        );
    }

    public function test_ignores_boilerplate_latin_lines(): void
    {
        $text = <<<'TXT'
MINISTRY OF INTERIOR
RESIDENCE PERMIT
محمد عبدالله
MOHAMMED ABDULLAH ALHAMDI
TXT;

        $this->assertSame(
            'Mohammed Abdullah Alhamdi',
            $this->service->parseEnglishNameFromOcrText($text)
        );
    }

    public function test_extracts_saudi_id_surname_comma_format(): void
    {
        $text = <<<'TXT'
المملكة العربية السعودية
وزارة الداخلية
الهوية الوطنية
منى بنت عبدالعزيز بن عبدالرزاق الدويش
ALDOUYSH, MONA ABDULAZIZ A
No
1064036146
TXT;

        $this->assertSame(
            'ALDOUYSH, MONA ABDULAZIZ A',
            $this->service->parseEnglishNameFromOcrText($text)
        );
    }
}
