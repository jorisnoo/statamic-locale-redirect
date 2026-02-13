<?php

namespace Noo\LocaleRedirect\Tests;

use Noo\LocaleRedirect\BrowserLocaleMatcher;

class BrowserLocaleMatcherTest extends TestCase
{
    public function test_it_matches_exact_browser_locale_to_site_locale(): void
    {
        $matcher = new BrowserLocaleMatcher();

        $result = $matcher->match(
            availableLocales: ['en', 'fr'],
            acceptLanguageHeader: 'fr'
        );

        $this->assertSame('fr', $result);
    }

    public function test_it_respects_quality_values_and_returns_highest_priority_match(): void
    {
        $matcher = new BrowserLocaleMatcher();

        $result = $matcher->match(
            availableLocales: ['en', 'fr'],
            acceptLanguageHeader: 'en-US,en;q=0.9,fr;q=0.8'
        );

        $this->assertSame('en', $result);
    }

    public function test_it_handles_partial_match_when_browser_sends_full_locale(): void
    {
        $matcher = new BrowserLocaleMatcher();

        $result = $matcher->match(
            availableLocales: ['en', 'fr', 'de'],
            acceptLanguageHeader: 'de-DE,de;q=0.9'
        );

        $this->assertSame('de', $result);
    }

    public function test_it_returns_null_when_no_locale_matches(): void
    {
        $matcher = new BrowserLocaleMatcher();

        $result = $matcher->match(
            availableLocales: ['en', 'fr'],
            acceptLanguageHeader: 'ja,zh;q=0.9'
        );

        $this->assertNull($result);
    }

    public function test_it_returns_null_for_empty_accept_language_header(): void
    {
        $matcher = new BrowserLocaleMatcher();

        $result = $matcher->match(
            availableLocales: ['en', 'fr'],
            acceptLanguageHeader: ''
        );

        $this->assertNull($result);
    }

    public function test_it_matches_lower_priority_locale_when_higher_is_unavailable(): void
    {
        $matcher = new BrowserLocaleMatcher();

        $result = $matcher->match(
            availableLocales: ['fr', 'de'],
            acceptLanguageHeader: 'en-US,en;q=0.9,fr;q=0.8'
        );

        $this->assertSame('fr', $result);
    }
}
