# Statamic Locale Redirect

A Statamic addon that automatically redirects visitors from `/` to their locale-specific home route based on their browser language preferences.

## Features

- Detects the visitor's preferred language from the `Accept-Language` header
- Matches it against your Statamic multi-site locales
- Redirects from `/` to the best-matching locale home URL (e.g. `/en`, `/fr`, `/de`)
- Preserves query parameters through the redirect
- Configurable locale exclusions and restrictions
- Optional permanent redirects from unprefixed URLs, for sites whose default site moved under a prefix
- Zero configuration required for basic usage

## Requirements

- PHP 8.2+
- Statamic 5 or 6

## Installation

```bash
composer require jorisnoo/statamic-locale-redirect
```

That's it. The addon registers itself automatically via Laravel's package discovery.

## How It Works

Only `GET` and `HEAD` requests handled by Statamic's frontend catch-all are eligible. The addon does not register `/`, override Laravel or `Route::statamic()` routes, or run in headless installations.

At the application root, the addon considers sites on the request's configured origin (scheme, host and port). Relative site URLs and a relative `fallback` are resolved against `APP_URL`, so include your application mount path there. A dedicated domain keeps its own homepage, even when the browser asks for another language.

Language preferences are considered in quality order. An exact locale (e.g. `en-GB`) wins before a language-only match; ties use site definition order, default site first. A match gets a temporary `302`. If nothing matches, the addon uses `fallback`, or the first site on the current origin. It never redirects to the same homepage. Both redirects and passthrough homepage responses vary on `Accept-Language`.

When a root site shares its origin with prefixed locales, link to `/?locale_redirect=off` in your language switcher to explicitly select the root homepage regardless of browser preferences. Statamic token requests also bypass language negotiation.

## Configuration

The addon works out of the box with no configuration. To customize behavior, publish the config file:

```bash
php artisan vendor:publish --tag=locale-redirect
```

This creates `config/statamic/locale-redirect.php`:

```php
return [
    'exclude' => [],
    'only' => [],
    'fallback' => null,
    'redirect_unprefixed' => false,
];
```

### Exclude Locales

Exclude site handles, full locale tags (`en_GB` or `en-GB`), or whole languages from matching:

```php
'exclude' => ['de', 'it'],
```

### Restrict to Specific Locales

Only allow redirection to specific locales. When set, `only` takes precedence over `exclude`:

```php
'only' => ['en', 'fr'],
```

### Redirect Unprefixed URLs

When a site that used to live at the root moves under a prefix (e.g. from `/` to `/de`), its old URLs stop working. Enable this option to redirect them:

```php
'redirect_unprefixed' => true,
```

Migration is scoped to the default site's configured origin. On that origin, any site rooted at `/` (or at the application mount) owns all its paths; otherwise existing site prefixes and explicitly registered routes take precedence. For an unclaimed path such as `/about`, a published default-site entry yields a `301` to its canonical prefixed route (`/de/about`). An entry configured to redirect elsewhere is still moved to **its route**, where Statamic applies its original destination and status. No query parameters are forwarded directly to an editorial redirect target.

Both migration and language redirects retain the original query string, including duplicate keys, order and encoding. The root stays with language negotiation, and unmatched paths, drafts and methods other than `GET` and `HEAD` fall through.

## Testing

```bash
./vendor/bin/pest
```

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.

## Maintainer releases

See [RELEASING.md](RELEASING.md) for versioning, changelog entries and the release command.
