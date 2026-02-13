<?php

namespace Noordermeer\LocaleRedirect;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleRedirectMiddleware
{
    public function __construct(
        private SiteLocaleReader $siteLocaleReader,
        private BrowserLocaleMatcher $browserLocaleMatcher,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->path() !== '/') {
            return $next($request);
        }

        if ($this->isBot($request)) {
            return $next($request);
        }

        $localeUrlMap = $this->siteLocaleReader->getLocaleUrlMap();
        $availableLocales = array_keys($localeUrlMap);

        $matchedLocale = $this->browserLocaleMatcher->match(
            availableLocales: $availableLocales,
            acceptLanguageHeader: $request->header('Accept-Language', ''),
        );

        if ($matchedLocale === null) {
            return $next($request);
        }

        return redirect($localeUrlMap[$matchedLocale], 302);
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
