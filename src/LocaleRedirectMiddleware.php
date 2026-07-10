<?php

namespace Noo\LocaleRedirect;

use Closure;
use Illuminate\Http\Request;

class LocaleRedirectMiddleware
{
    public function __construct(
        private LocaleRedirectController $localeRedirectController,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        return ($this->localeRedirectController)($request) ?? $next($request);
    }
}
