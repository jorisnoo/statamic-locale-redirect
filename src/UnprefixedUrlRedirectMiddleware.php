<?php

namespace Noo\LocaleRedirect;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Facades\URL;

/**
 * For sites whose default site moved from the root to a prefix (e.g. "/" to
 * "/de"): a URL outside every site prefix that names a published entry on the
 * default site is moved permanently to that entry's prefixed address. The
 * root is left to the locale redirect, anything else falls through.
 */
class UnprefixedUrlRedirectMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! config('statamic.locale-redirect.redirect_unprefixed') || ! $request->isMethodSafe()) {
            return $next($request);
        }

        $path = '/' . ltrim($request->path(), '/');

        if ($path === '/' || $this->isUnderASitePrefix($path)) {
            return $next($request);
        }

        $entry = Entry::findByUri($path, Site::default()->handle());

        if ($entry?->status() !== 'published') {
            return $next($request);
        }

        $redirectUrl = $entry->url();

        $queryString = $request->getQueryString();
        if ($queryString !== null && $queryString !== '') {
            $redirectUrl .= '?' . $queryString;
        }

        return redirect($redirectUrl, 301);
    }

    private function isUnderASitePrefix(string $path): bool
    {
        return Site::all()->contains(function ($site) use ($path) {
            $prefix = rtrim(URL::makeRelative($site->url()), '/');

            return $prefix !== '' && ($path === $prefix || str_starts_with($path, $prefix . '/'));
        });
    }
}
