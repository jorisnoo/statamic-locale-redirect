# Changelog

All notable changes to this project will be documented in this file.

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
