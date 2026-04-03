# Changelog

All notable changes to this project will be documented in this file.

## [0.1.2](https://github.com/jorisnoo/statamic-locale-redirect/releases/tag/v0.1.2) (2026-04-03)

### Bug Fixes

- prevent caching of locale redirects ([5b17d61](https://github.com/jorisnoo/statamic-locale-redirect/commit/5b17d610c622a3efe06e19c1b5a54a6abde282e0))

### Chores

- add dependabot configuration and auto-merge workflow ([0bf1b5b](https://github.com/jorisnoo/statamic-locale-redirect/commit/0bf1b5b891547eadb5c88f2c86c554df8187425c))
## [0.1.1](https://github.com/jorisnoo/statamic-locale-redirect/releases/tag/v0.1.1) (2026-03-17)

### Code Refactoring

- **routes:** remove route name from locale redirect ([03fe519](https://github.com/jorisnoo/statamic-locale-redirect/commit/03fe519c60e63afd1f169ae7f33e7b7e7b3f6d41))

### Chores

- add shipmark release configuration ([92f6662](https://github.com/jorisnoo/statamic-locale-redirect/commit/92f66629b9e1ee11e7432162d5579144f97b4d93))
## [0.1.0](https://github.com/jorisnoo/statamic-locale-redirect/releases/tag/v0.1.0) (2026-03-17)

### Features

- US-005 - Convert ServiceProviderTest to Pest ([1855c1f](https://github.com/jorisnoo/statamic-locale-redirect/commit/1855c1f8d863f512b92d9a1062e20699d30fc9ee))
- US-004 - Convert LocaleRedirectMiddlewareTest to Pest ([a3f1963](https://github.com/jorisnoo/statamic-locale-redirect/commit/a3f196377eac3f19794fa77b35c889897af018ba))
- US-003 - Convert BrowserLocaleMatcherTest to Pest ([1237aec](https://github.com/jorisnoo/statamic-locale-redirect/commit/1237aec081af72aeb59c80d729f129b46efca01d))
- US-002 - Convert SiteLocaleReaderTest to Pest ([a38887d](https://github.com/jorisnoo/statamic-locale-redirect/commit/a38887dabf5e1c8c6b40459a909e1e19082858c3))
- US-001 - Install Pest and Configure the Test Runner ([ca9020b](https://github.com/jorisnoo/statamic-locale-redirect/commit/ca9020b212aa47a4de476f6ca1ed435a4733348a))
- US-007 - Publishable Config File ([56b0aec](https://github.com/jorisnoo/statamic-locale-redirect/commit/56b0aecd9a1cd8eadb8560edda873154f91de9a3))
- US-006 - Optional Config — Restrict to Specific Locales ([8522af2](https://github.com/jorisnoo/statamic-locale-redirect/commit/8522af2e4c1d606d8e37bf74d79f6777c071c86c))
- US-005 - Optional Config — Exclude Locales ([5d42410](https://github.com/jorisnoo/statamic-locale-redirect/commit/5d424104d1bdc090d9108ae1430aad1358b9616f))
- US-004 - Redirect Home Route to Matched Locale ([aec2ec4](https://github.com/jorisnoo/statamic-locale-redirect/commit/aec2ec4e5a21ce0f14400c579dd515fd07b213fe))
- US-003 - Match Browser Locale to Site Locale ([c4666bf](https://github.com/jorisnoo/statamic-locale-redirect/commit/c4666bf42afef597d9b0695a67a27e01a0e2b6d3))
- US-002 - Read Statamic Site Locales ([b7502b9](https://github.com/jorisnoo/statamic-locale-redirect/commit/b7502b92e2d6f36f181b4033a154841677eab626))
- US-001 - Package Scaffolding ([625fcfc](https://github.com/jorisnoo/statamic-locale-redirect/commit/625fcfcac9499511d1c77ce7bf9a7d300a1434cc))

### Bug Fixes

- update test runner from PHPUnit to Pest in CI workflow ([326af30](https://github.com/jorisnoo/statamic-locale-redirect/commit/326af30d0a0e593676a3b09051a068249c8202a8))

### Code Refactoring

- remove external language detection dependency and implement native Accept-Language parsing ([1c69806](https://github.com/jorisnoo/statamic-locale-redirect/commit/1c6980670edaf34d03d66b90e2e2e83fbba379de))
- convert locale redirect from middleware to controller with configurable fallback URL ([7452f81](https://github.com/jorisnoo/statamic-locale-redirect/commit/7452f81342d96c305e8f1783896ea9fc51536790))
- move config to statamic namespace and update publish tag ([835a370](https://github.com/jorisnoo/statamic-locale-redirect/commit/835a370113f96a3d54ce0f2a0034fd3864247c53))

### Chores

- update .gitignore with comprehensive ignore rules and rename composer package ([f29075e](https://github.com/jorisnoo/statamic-locale-redirect/commit/f29075ea876138a3ac01b32f69d5acf5e40d22b9))
- remove badge links from README ([7a41f15](https://github.com/jorisnoo/statamic-locale-redirect/commit/7a41f153068d28d32f9bd4b420470d9489b222b1))
- add project meta files (editorconfig, CI workflow, changelog, license, readme) ([93c2dfe](https://github.com/jorisnoo/statamic-locale-redirect/commit/93c2dfed8c85dfc3d12b52dd08a50b3e27222085))
- rename namespace from Noordermeer to Noo, update metadata, and remove empty routes file ([c905c8f](https://github.com/jorisnoo/statamic-locale-redirect/commit/c905c8fc0d2c2f9ae8fbd2c6c45c8301008c327a))
The format is based on [Keep a Changelog](https://keepachangelog.com), and this project adheres to [Semantic Versioning](https://semver.org).

## [1.0.0] - Unreleased

### Added

- Package scaffolding with Statamic addon service provider
- Read Statamic multi-site locales and their URLs
- Match browser `Accept-Language` header to available site locales
- Redirect from `/` to the best-matching locale home URL (302)
- Exclude specific locales from redirect targets via config
- Restrict redirection to specific locales via `only` config option
- Publishable configuration file (`locale-redirect.php`)
- Bot and crawler detection to skip redirection for search engines
