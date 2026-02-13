<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Exclude Locales
    |--------------------------------------------------------------------------
    |
    | An array of locale codes to exclude from being redirect targets.
    | For example: ['de', 'it']
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
    | The URL to redirect to when no browser locale matches or when a bot
    | visits the root URL. When null, the default Statamic site URL is used.
    |
    */

    'fallback' => null,

];
