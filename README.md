# Statamic Locale Redirect

[![Latest Version on Packagist](https://img.shields.io/packagist/v/noordermeer/statamic-locale-redirect.svg?style=flat-square)](https://packagist.org/packages/noordermeer/statamic-locale-redirect)
[![Tests](https://github.com/noordermeer/statamic-locale-redirect/actions/workflows/tests.yml/badge.svg)](https://github.com/noordermeer/statamic-locale-redirect/actions/workflows/tests.yml)
[![License](https://img.shields.io/packagist/l/noordermeer/statamic-locale-redirect.svg?style=flat-square)](https://packagist.org/packages/noordermeer/statamic-locale-redirect)

A Statamic addon that automatically redirects visitors from `/` to their locale-specific home route based on their browser language preferences.

## Features

- Detects the visitor's preferred language from the `Accept-Language` header
- Matches it against your Statamic multi-site locales
- Redirects from `/` to the best-matching locale home URL (e.g. `/en`, `/fr`, `/de`)
- Skips bots and crawlers to preserve SEO
- Configurable locale exclusions and restrictions
- Zero configuration required for basic usage

## Requirements

- PHP 8.2+
- Statamic 5 or 6

## Installation

```bash
composer require noordermeer/statamic-locale-redirect
```

That's it. The addon registers itself automatically via Laravel's package discovery.

## How It Works

When a visitor hits your site's root URL (`/`), the middleware:

1. Reads the `Accept-Language` header from the browser
2. Fetches all configured Statamic site locales and their URLs
3. Finds the best match between browser preferences and available locales
4. Issues a `302` redirect to the matched locale's home URL

If no match is found or the request isn't to `/`, the request passes through unchanged.

### Bot Detection

Bots and crawlers (Googlebot, Bingbot, etc.) are automatically excluded from redirection so they can index your root URL normally.

## Configuration

The addon works out of the box with no configuration. To customize behavior, publish the config file:

```bash
php artisan vendor:publish --tag=locale-redirect-config
```

This creates `config/locale-redirect.php`:

```php
return [
    'exclude' => [],
    'only' => [],
];
```

### Exclude Locales

Prevent specific locales from being redirect targets:

```php
'exclude' => ['de', 'it'],
```

### Restrict to Specific Locales

Only allow redirection to specific locales. When set, `only` takes precedence over `exclude`:

```php
'only' => ['en', 'fr'],
```

## Testing

```bash
./vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
