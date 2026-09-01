<?php

declare(strict_types=1);

namespace App\Support;

final class WhatsAppCsvPhoneParser
{
    public const MAX_PHONES = 250;

    /**
     * Convert Eastern Arabic / Persian digits to ASCII.
     */
    public static function toAsciiDigits(string $value): string
    {
        return strtr($value, [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        ]);
    }

    /**
     * @return list<string>
     */
    public static function extractPhones(string $csv): array
    {
        $csv = self::toAsciiDigits($csv);
        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv) ?? $csv;

        $lines = preg_split("/\r\n|\n|\r/", $csv) ?: [];
        $lines = array_values(array_filter($lines, static fn (string $line): bool => trim($line) !== ''));

        if ($lines === []) {
            return [];
        }

        $delimiter = self::detectDelimiter($lines[0]);
        $rows = [];
        foreach ($lines as $line) {
            $rows[] = str_getcsv($line, $delimiter, '"', '\\');
        }

        $phoneColumn = self::detectPhoneColumn($rows[0]);
        $start = $phoneColumn !== null ? 1 : 0;

        $phones = [];
        $seen = [];

        for ($i = $start, $count = count($rows); $i < $count; $i++) {
            $cells = $phoneColumn !== null
                ? [($rows[$i][$phoneColumn] ?? '')]
                : $rows[$i];

            foreach ($cells as $cell) {
                $candidate = trim((string) $cell);
                if (! self::looksLikePhone($candidate)) {
                    continue;
                }

                $digits = preg_replace('/\D+/', '', $candidate) ?? '';
                $key = strlen($digits) >= 9 ? substr($digits, -9) : $digits;
                if ($key === '' || isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;
                $phones[] = $candidate;

                if (count($phones) >= self::MAX_PHONES) {
                    return $phones;
                }
            }
        }

        return $phones;
    }

    private static function detectDelimiter(string $headerLine): string
    {
        $comma = substr_count($headerLine, ',');
        $semi = substr_count($headerLine, ';');
        $tab = substr_count($headerLine, "\t");

        if ($tab > $comma && $tab > $semi) {
            return "\t";
        }

        if ($semi > $comma) {
            return ';';
        }

        return ',';
    }

    /**
     * @param  list<string|null>  $header
     */
    private static function detectPhoneColumn(array $header): ?int
    {
        foreach ($header as $index => $cell) {
            $label = strtolower(trim((string) $cell));
            $label = str_replace(['_', '-'], ' ', $label);

            if (preg_match('/^(phone|mobile|msisdn|whatsapp|رقم|جوال|هاتف|موبايل)(\s|$)/u', $label) === 1) {
                return (int) $index;
            }

            if (in_array($label, ['phone', 'phone number', 'mobile', 'mobile number', 'الجوال', 'رقم الجوال'], true)) {
                return (int) $index;
            }
        }

        return null;
    }

    private static function looksLikePhone(string $value): bool
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        return strlen($digits) >= 8 && strlen($digits) <= 15;
    }
}
