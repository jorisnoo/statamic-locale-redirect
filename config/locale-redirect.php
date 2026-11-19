<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Exclude Locales
    |--------------------------------------------------------------------------
    |
    | Site handles, full locale tags, or languages to exclude from matching.
    | For example: ['swiss_german', 'en-GB', 'it']
    |
    */

    'exclude' => [],

    /*
    |--------------------------------------------------------------------------
    | Only Locales
    |--------------------------------------------------------------------------
    |
    | An array of locale codes to restrict redirection to. When set, only
    | these locales will be considered as redirect targets. Takes precedence
    | over "exclude" if both are set.
    |
    */

    'only' => [],

    /*
    |--------------------------------------------------------------------------
    | Fallback URL
    |--------------------------------------------------------------------------
    |
    | The URL to redirect to when no browser locale matches. When null, the
    | first site on the current origin is used (the default when present).
    |
    */

    'fallback' => null,

    /*
    |--------------------------------------------------------------------------
    | Redirect Unprefixed URLs
    |--------------------------------------------------------------------------
    |
    | For sites whose default site moved from the root to a prefix, e.g. from
    | "/" to "/de". When enabled, a URL outside every site prefix that matches
    | a published entry on the default site is redirected (301) to that
    | entry's prefixed URL: "/about" becomes "/de/about".
    |
    */

    'redirect_unprefixed' => false,

];
