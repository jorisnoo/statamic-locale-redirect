<?php

namespace Noo\LocaleRedirect;

class BrowserLocaleMatcher
{
    /**
     * @param array<int, string> $availableLocales
     */
    public function match(array $availableLocales, string $acceptLanguageHeader): ?string
    {
        if ($acceptLanguageHeader === '' || $availableLocales === []) {
            return null;
        }

        $preferences = $this->parseAcceptLanguage($acceptLanguageHeader);

        $tags = array_map(self::normalize(...), $availableLocales);
        foreach ($preferences as $preferred) {
            $tag = self::normalize($preferred);
            $exact = array_search($tag, $tags, true);
            if ($exact !== false) {
                return $availableLocales[$exact];
            }

            $language = explode('-', $tag, 2)[0];
            foreach ($tags as $key => $candidate) {
                if (explode('-', $candidate, 2)[0] === $language) {
                    return $availableLocales[$key];
                }
            }
        }

        return null;
    }

    public static function normalize(string $locale): string
    {
        return strtolower(str_replace('_', '-', $locale));
    }

    /**
     * Parse Accept-Language header into a priority-sorted list of language codes.
     *
     * @return array<int, string>
     */
    private function parseAcceptLanguage(string $header): array
    {
        $entries = [];

        foreach (explode(',', $header) as $part) {
            $part = trim($part);

            if ($part === '') {
                continue;
            }

            $segments = explode(';', $part);
            $locale = strtolower(trim($segments[0]));
            $quality = 1.0;

            foreach (array_slice($segments, 1) as $segment) {
                $segment = trim($segment);
                if (str_starts_with(strtolower($segment), 'q=')) {
                    $quality = min(1.0, (float) substr($segment, 2));
                }
            }

            if ($quality > 0) {
                $entries[] = ['locale' => $locale, 'quality' => $quality];
            }
        }

        usort($entries, fn ($a, $b) => $b['quality'] <=> $a['quality']);

        return array_column($entries, 'locale');
    }
}
