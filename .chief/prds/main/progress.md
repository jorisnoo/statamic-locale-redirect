## Codebase Patterns
- Package namespace: `Noordermeer\LocaleRedirect`
- Service provider extends `Statamic\Providers\AddonServiceProvider` and uses `bootAddon()` (not `boot()`)
- Config key: `locale-redirect` (published tag: `locale-redirect-config`)
- Language detection library is `koenster/php-language-detection` (note: PRD says "koester" but actual package vendor is "koenster")
- BrowserLocalization API: `setAvailable()`, `setDefault()`, `setPreferences()`, `detect()`
- Statamic addon routes go in `$routes` property array on service provider (e.g., `'web' => __DIR__.'/../routes/web.php'`)
- Composer requires pixelfear/composer-dist-plugin allowance for Statamic CMS dist
- Test infrastructure: `TestCase` extends `Statamic\Testing\AddonTestCase`, configure sites with `Site::setSites()` + `config(['statamic.system.multisite' => true])`
- `Site::url()` uses `URL::tidy()` which strips trailing slashes (except for `/`)
- `Site::shortLocale()` returns the 2-letter language code from the full locale (e.g., `en_US` → `en`)

---

## 2026-02-13 - US-001
- Implemented package scaffolding for the Statamic Locale Redirect addon
- Files changed:
  - `composer.json` — package metadata, dependencies (PHP 8.2+, Statamic 5/6, koenster/php-language-detection), autoload, extra.statamic/extra.laravel
  - `src/ServiceProvider.php` — extends AddonServiceProvider, registers routes, merges/publishes config
  - `config/locale-redirect.php` — default config with empty `exclude` and `only` arrays
  - `routes/web.php` — placeholder web routes file
  - `.gitignore` — added vendor/ and composer.lock exclusions
- **Learnings for future iterations:**
  - The PRD references `koester/php-language-detection` but the actual packagist name is `koenster/php-language-detection`
  - Statamic CMS requires allowing `pixelfear/composer-dist-plugin` in composer config
  - Use `bootAddon()` instead of `boot()` in Statamic addon service providers
  - The config file and publishable config are already set up in US-001, which covers US-007 acceptance criteria too
---

## 2026-02-13 - US-002
- Implemented `SiteLocaleReader` class that reads Statamic site locales and returns a locale => URL mapping
- Files changed:
  - `src/SiteLocaleReader.php` — new class with `getLocaleUrlMap()` method using `Site::all()` facade
  - `tests/TestCase.php` — base test case extending `Statamic\Testing\AddonTestCase`
  - `tests/SiteLocaleReaderTest.php` — 4 tests covering locale mapping, short locale extraction, prefix-based setups, and all-sites coverage
  - `phpunit.xml` — PHPUnit configuration for the package
- **Learnings for future iterations:**
  - Statamic's `URL::tidy()` strips trailing slashes from site URLs (except root `/`), so test expectations should not include trailing slashes
  - Use `Site::setSites()` with config array to set up test sites; each site needs `name`, `url`, and `locale` keys
  - Set `config(['statamic.system.multisite' => true])` when configuring multiple sites in tests
  - `Site::shortLocale()` extracts the 2-letter language code from full locale strings like `en_US`
---
