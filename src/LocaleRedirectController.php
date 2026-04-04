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
        $localeUrlMap = $this->siteLocaleReader->getLocaleUrlMap();
        $localeUrlMap = $this->filterLocales($localeUrlMap);
        $availableLocales = array_keys($localeUrlMap);

        $matchedLocale = $this->browserLocaleMatcher->match(
            availableLocales: $availableLocales,
            acceptLanguageHeader: $request->header('Accept-Language', ''),
        );

        $redirectUrl = $matchedLocale !== null
            ? $localeUrlMap[$matchedLocale]
            : $this->fallbackUrl();

        // Preserve query parameters
        $queryString = $request->getQueryString();
        if ($queryString !== null && $queryString !== '') {
            $redirectUrl .= (str_contains($redirectUrl, '?') ? '&' : '?') . $queryString;
        }

        return $this->noCacheRedirect($redirectUrl);
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

    private function noCacheRedirect(string $url): RedirectResponse
    {
        return redirect($url, 302)->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Vary' => 'Accept-Language',
        ]);
    }
}
