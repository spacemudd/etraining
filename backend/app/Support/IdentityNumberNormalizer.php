<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Back\Trainee;

class IdentityNumberNormalizer
{
    /**
     * Arabic-Indic (U+0660–U+0669) and Extended Arabic-Indic / Persian (U+06F0–U+06F9).
     */
    private const ARABIC_TO_WESTERN = [
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
    ];

    private const WESTERN_TO_ARABIC_INDIC = [
        '0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤',
        '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩',
    ];

    private const WESTERN_TO_EXTENDED_ARABIC_INDIC = [
        '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
        '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
    ];

    public static function normalize(string $identity): string
    {
        $identity = trim($identity);

        if (str_starts_with($identity, "\xEF\xBB\xBF")) {
            $identity = substr($identity, 3);
        }

        $identity = strtr($identity, self::ARABIC_TO_WESTERN);
        $identity = preg_replace('/[\x{200E}\x{200F}\x{202A}-\x{202E}\x{FEFF}]/u', '', $identity) ?? $identity;
        $identity = preg_replace('/[^0-9]/', '', $identity) ?? '';

        return $identity;
    }

    /**
     * @return array<int, string>
     */
    public static function storageVariants(string $normalizedWestern): array
    {
        if ($normalizedWestern === '') {
            return [];
        }

        return array_values(array_unique([
            $normalizedWestern,
            strtr($normalizedWestern, self::WESTERN_TO_ARABIC_INDIC),
            strtr($normalizedWestern, self::WESTERN_TO_EXTENDED_ARABIC_INDIC),
        ]));
    }

    public static function findTraineeByIdentity(string $rawIdentity): ?Trainee
    {
        $normalized = self::normalize($rawIdentity);

        if ($normalized === '') {
            return null;
        }

        foreach (self::storageVariants($normalized) as $variant) {
            $trainee = Trainee::withTrashed()->where('identity_number', $variant)->first();

            if ($trainee) {
                return $trainee;
            }
        }

        return null;
    }
}
