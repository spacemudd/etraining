<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Back\Trainee;

class WhatsAppTemplateTags
{
    /**
     * @return array<string, array{label: string, example: string, example_en: string}>
     */
    public static function definitions(): array
    {
        if (self::$definitionsOverride !== null) {
            return self::$definitionsOverride;
        }

        /** @var array<string, array{label?: string, example?: string, example_en?: string}> $tags */
        $tags = config('whatsapp.template_auto_tags', []);

        return is_array($tags) ? $tags : [];
    }

    /**
     * @param  array<string, array{label?: string, example?: string, example_en?: string}>|null  $definitions
     */
    public static function setDefinitionsForTesting(?array $definitions): void
    {
        self::$definitionsOverride = $definitions;
    }

    /** @var array<string, array{label?: string, example?: string, example_en?: string}>|null */
    private static ?array $definitionsOverride = null;

    /**
     * @return array<int, array{tag: string, label: string, example: string}>
     */
    public static function availableForUi(string $language = 'ar'): array
    {
        $items = [];

        foreach (self::definitions() as $tag => $meta) {
            $items[] = [
                'tag' => $tag,
                'label' => (string) ($meta['label'] ?? $tag),
                'example' => self::defaultExample($tag, $language),
                'placeholder' => '{{' . $tag . '}}',
            ];
        }

        return $items;
    }

    public static function isAutoTag(string $tag): bool
    {
        return array_key_exists($tag, self::definitions());
    }

    public static function defaultExample(string $tag, string $language = 'ar'): string
    {
        $meta = self::definitions()[$tag] ?? [];

        if (str_starts_with(strtolower($language), 'en')) {
            return (string) ($meta['example_en'] ?? $meta['example'] ?? $tag);
        }

        return (string) ($meta['example'] ?? $meta['example_en'] ?? $tag);
    }

    public static function resolve(string $tag, ?Trainee $trainee): ?string
    {
        if (! $trainee || ! self::isAutoTag($tag)) {
            return null;
        }

        return match ($tag) {
            'trainee_name' => filled($trainee->name) ? (string) $trainee->name : null,
            'trainee_english_name' => filled($trainee->english_name) ? (string) $trainee->english_name : null,
            'trainee_phone' => filled($trainee->phone) ? (string) $trainee->phone : null,
            'trainee_identity' => filled($trainee->identity_number) ? (string) $trainee->identity_number : null,
            'company_name' => filled(optional($trainee->company)->name_ar)
                ? (string) $trainee->company->name_ar
                : null,
            default => null,
        };
    }

    /**
     * Convert named placeholders (e.g. {{trainee_name}}) into Meta numbered ones ({{1}}).
     *
     * @param  array<string, string>  $variableSamples
     * @return array{
     *     body: string,
     *     bindings: array<string, string>,
     *     samples: array<string, string>
     * }
     */
    public static function normalizeBody(string $body, array $variableSamples = [], string $language = 'ar'): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $body, $matches);

        $tokens = [];
        foreach ($matches[1] ?? [] as $token) {
            if (! in_array($token, $tokens, true)) {
                $tokens[] = $token;
            }
        }

        $bindings = [];
        $samples = [];

        foreach ($tokens as $index => $token) {
            $temp = '%%WA_VAR_' . $index . '%%';
            $body = preg_replace(
                '/\{\{\s*' . preg_quote($token, '/') . '\s*\}\}/',
                $temp,
                $body
            ) ?? $body;
        }

        foreach ($tokens as $index => $token) {
            $number = (string) ($index + 1);
            $body = str_replace('%%WA_VAR_' . $index . '%%', '{{' . $number . '}}', $body);

            if (! ctype_digit($token)) {
                $bindings[$number] = $token;
            }

            $sample = trim((string) ($variableSamples[$token] ?? ''));

            if ($sample === '' && self::isAutoTag($token)) {
                $sample = self::defaultExample($token, $language);
            }

            $samples[$number] = $sample !== '' ? $sample : ('example' . $number);
        }

        return [
            'body' => $body,
            'bindings' => $bindings,
            'samples' => $samples,
        ];
    }

    /**
     * @param  array<string, string>  $bindings
     */
    public static function applyBindingsToBody(string $body, array $bindings): string
    {
        foreach ($bindings as $number => $tag) {
            if (! is_string($tag) || $tag === '' || ctype_digit($tag)) {
                continue;
            }

            $body = str_replace('{{' . $number . '}}', '{{' . $tag . '}}', $body);
        }

        return $body;
    }
}
