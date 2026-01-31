<?php

namespace App\Services;

class ContentFilter
{
    /**
     * Redact banned words with asterisks. Case-insensitive, whole-word match.
     *
     * @param string $text
     * @return array{clean:string, redacted:bool, hits:int}
     */
    public static function redact(string $text): array
    {
        $cfg = config('profanity.banned_words');
        $words = is_array($cfg) ? $cfg : [];
        // normalize list
        $words = array_values(array_unique(array_filter(array_map('strval', $words))));
        if (empty($words)) {
            return ['clean' => $text, 'redacted' => false, 'hits' => 0];
        }

        $hits = 0;
        $clean = $text;

        foreach ($words as $w) {
            if ($w === '') {
                continue;
            }

            // Prefer whole-word matching for normal words (letters/numbers/underscore).
            // If the banned term contains non-word chars, fall back to a simple match.
            $quoted = preg_quote($w, '/');
            $useWordBoundaries = (bool) preg_match('/^\w+$/u', $w);
            $pattern = $useWordBoundaries
                ? '/\b' . $quoted . '\b/iu'
                : '/' . $quoted . '/iu';

            $clean = preg_replace_callback($pattern, function () use (&$hits) {
                $hits++;
                return '****';
            }, $clean);
        }

        return [
            'clean' => $clean,
            'redacted' => $hits > 0,
            'hits' => $hits,
        ];
    }
}
