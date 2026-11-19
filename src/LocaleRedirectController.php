<?php

namespace Noo\LocaleRedirect;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Statamic\Facades\Site;

class LocaleRedirectController
{
    public function __construct(
        private BrowserLocaleMatcher $browserLocaleMatcher,
    ) {}

    public function __invoke(Request $request): ?RedirectResponse
    {
        if ($request->query('locale_redirect') === 'off' || $request->statamicToken()) {
            return null;
        }

        $sites = Site::all()->filter(fn ($site) => RedirectTarget::sameOrigin(
            $request, RedirectTarget::absolute($site->url())
        ));
        if ($sites->isEmpty()) {
            return null;
        }

        // A dedicated site origin owns its homepage, regardless of language or fallback.
        $first = $sites->first();
        if ($sites->count() === 1 && RedirectTarget::isCurrent($request, RedirectTarget::absolute($first->url()))) {
            return null;
        }

        $eligible = $sites->filter(fn ($site) => $this->eligible($site));
        $locale = $this->browserLocaleMatcher->match(
            $eligible->map(fn ($site) => $site->locale())->values()->all(),
            $request->header('Accept-Language', ''),
        );
        $site = $eligible->first(fn ($site) => $site->locale() === $locale);
        $target = RedirectTarget::absolute($site?->url() ?? config('statamic.locale-redirect.fallback') ?? $first->url());

        if (RedirectTarget::isCurrent($request, $target)) {
            return null;
        }

        return redirect()->away(RedirectTarget::withQuery($request, $target), 302, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    private function eligible(\Statamic\Sites\Site $site): bool
    {
        $only = config('statamic.locale-redirect.only', []);
        $filters = $only ?: config('statamic.locale-redirect.exclude', []);
        $aliases = [$site->handle(), $site->locale(), $site->shortLocale()];
        $matches = array_intersect(
            array_map(BrowserLocaleMatcher::normalize(...), $aliases),
            array_map(BrowserLocaleMatcher::normalize(...), $filters),
        );

        return $only ? (bool) $matches : ! $matches;
    }
}
