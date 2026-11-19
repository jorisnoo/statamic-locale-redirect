<?php

namespace Noo\LocaleRedirect;

use Closure;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;

class UnprefixedUrlRedirectMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (! config('statamic.locale-redirect.redirect_unprefixed')) {
            return $next($request);
        }

        $site = Site::default();
        $base = RedirectTarget::absolute($site->url());
        if (! RedirectTarget::sameOrigin($request, $base) || $this->ownedBySite($request)) {
            return $next($request);
        }

        $entry = Entry::findByUri('/' . ltrim($request->path(), '/'), $site->handle());

        if ($entry?->status() !== 'published') {
            return $next($request);
        }

        // Move the route, not the destination of an entry's editorial redirect.
        // Statamic still applies that redirect's own status, query and fragment.
        $target = rtrim($base, '/') . '/' . ltrim($entry->uri(), '/');
        if (RedirectTarget::isCurrent($request, $target)) {
            return $next($request);
        }

        return redirect()->away(RedirectTarget::withQuery($request, $target), 301);
    }

    private function ownedBySite(Request $request): bool
    {
        $path = rtrim(parse_url($request->url(), PHP_URL_PATH) ?? '', '/') . '/';

        return Site::all()->contains(function ($site) use ($request, $path) {
            $home = RedirectTarget::absolute($site->url());
            $prefix = rtrim(parse_url($home, PHP_URL_PATH) ?? '', '/') . '/';

            return RedirectTarget::sameOrigin($request, $home) && str_starts_with($path, $prefix);
        });
    }
}
