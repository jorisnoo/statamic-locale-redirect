<?php

namespace Noo\LocaleRedirect;

use Closure;
use Illuminate\Http\Request;

class LocaleRedirectMiddleware
{
    public function __construct(
        private LocaleRedirectController $localeRedirectController,
        private UnprefixedUrlRedirectMiddleware $migration,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->routeIs('statamic.site') || ! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        if ($request->path() !== '/') {
            return $this->migration->handle($request, $next);
        }

        $response = ($this->localeRedirectController)($request) ?? $next($request);
        $response->setVary('Accept-Language', false);

        return $response;
    }
}
