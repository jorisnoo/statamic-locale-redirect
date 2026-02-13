<?php

namespace Noordermeer\LocaleRedirect;

use Statamic\Facades\Site;

class SiteLocaleReader
{
    /** @return array<string, string> */
    public function getLocaleUrlMap(): array
    {
        return Site::all()->mapWithKeys(function ($site) {
            return [$site->shortLocale() => $site->url()];
        })->all();
    }
}
