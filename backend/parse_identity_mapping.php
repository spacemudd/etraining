<?php

declare(strict_types=1);

/**
 * One-off script: reads TSV (identity_number TAB english_name) from first argument,
 * converts Arabic numerals in identity to Western, and overwrites
 * app/Data/IdentityEnglishNameMapping.php with the full array.
 * Usage: php parse_identity_mapping.php path/to/data.txt
 * To add more identities: append lines to the TSV file and run again.
 */

$dataFile = $argv[1] ?? '';
if ($dataFile !== '' && is_readable($dataFile)) {
    $lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
} else {
    fwrite(STDERR, "Usage: php parse_identity_mapping.php <path-to-tsv-file>\n");
    exit(1);
}

$arabicToWestern = [
    '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
    '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
    '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
    '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
];

function normalizeIdentity(string $id, array $arabicToWestern): string {
    $id = strtr($id, $arabicToWestern);
    $id = preg_replace('/[^0-9]/', '', $id);
    return trim($id);
}

$mapping = [];

foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $parts = preg_split('/\t/', $line, 2);
    $identity = isset($parts[0]) ? trim($parts[0]) : '';
    $name = isset($parts[1]) ? trim($parts[1]) : '';

    // Skip empty identity; skip "deleted" suffix
    $identity = preg_replace('/-deleted$/', '', $identity);
    $identity = normalizeIdentity($identity, $arabicToWestern);
    if ($identity === '') {
        continue;
    }

    $name = trim($name);
    if ($name === '' || $name === 'لا يوجد') {
        $name = 'Not Available';
    }
    // Strip trailing Arabic comment if present (e.g. "هي دي الهويات بالأسماء ضيفهم")
    if (preg_match('/^(.+?)\s+هي دي الهويات/u', $name, $m)) {
        $name = trim($m[1]);
    }
    $name = str_replace(["\\", "'"], ["\\\\", "\\'"], $name);
    $mapping[$identity] = $name;
}

$outDir = __DIR__ . '/app/Data';
$outFile = $outDir . '/IdentityEnglishNameMapping.php';

$entries = [];
foreach ($mapping as $id => $name) {
    $entries[] = "            '" . $id . "' => '" . $name . "'";
}

$php = <<<PHP
<?php

declare(strict_types=1);

namespace App\Data;

class IdentityEnglishNameMapping
{
    /**
     * Mapping of identity_number => english_name for trainees.
     * Fill this array then run: php artisan trainees:update-english-names-from-mapping
     *
     * @return array<string, string>
     */
    public static function getMapping(): array
    {
        return [
PHP . "\n" . implode(",\n", $entries) . "\n        ];\n    }\n}\n";

file_put_contents($outFile, $php);
echo "Written " . count($mapping) . " entries to {$outFile}\n";
