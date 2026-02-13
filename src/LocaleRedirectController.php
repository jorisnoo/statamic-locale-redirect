<?php

namespace Noo\LocaleRedirect;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Statamic\Facades\Site;

class LocaleRedirectController
{
    public function __construct(
        private SiteLocaleReader $siteLocaleReader,
        private BrowserLocaleMatcher $browserLocaleMatcher,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $fallbackUrl = $this->fallbackUrl();

        if ($this->isBot($request)) {
            return redirect($fallbackUrl, 302);
        }

        $localeUrlMap = $this->siteLocaleReader->getLocaleUrlMap();
        $localeUrlMap = $this->filterLocales($localeUrlMap);
        $availableLocales = array_keys($localeUrlMap);

        $matchedLocale = $this->browserLocaleMatcher->match(
            availableLocales: $availableLocales,
            acceptLanguageHeader: $request->header('Accept-Language', ''),
        );

        if ($matchedLocale === null) {
            return redirect($fallbackUrl, 302);
        }

        return redirect($localeUrlMap[$matchedLocale], 302);
    }

    private function fallbackUrl(): string
    {
        return config('statamic.locale-redirect.fallback') ?? Site::default()->url();
    }

    /**
     * @param array<string, string> $localeUrlMap
     * @return array<string, string>
     */
    private function filterLocales(array $localeUrlMap): array
    {
        $only = config('statamic.locale-redirect.only', []);

        if (! empty($only)) {
            return array_intersect_key($localeUrlMap, array_flip($only));
        }

        $exclude = config('statamic.locale-redirect.exclude', []);

        if (! empty($exclude)) {
            return array_diff_key($localeUrlMap, array_flip($exclude));
        }

        return $localeUrlMap;
    }

    private function isBot(Request $request): bool
    {
        $userAgent = $request->header('User-Agent', '');

        if ($userAgent === '') {
            return true;
        }

        $botPatterns = [
            'bot',
            'crawl',
            'spider',
            'slurp',
            'mediapartners',
            'facebookexternalhit',
            'embedly',
            'quora link preview',
            'outbrain',
            'pinterest',
            'semrush',
            'ahrefs',
        ];

        $userAgentLower = strtolower($userAgent);

        foreach ($botPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
