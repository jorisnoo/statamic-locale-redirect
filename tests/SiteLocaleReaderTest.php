<?php

namespace Noordermeer\LocaleRedirect\Tests;

use Noordermeer\LocaleRedirect\SiteLocaleReader;
use Statamic\Facades\Site;

class SiteLocaleReaderTest extends TestCase
{
    private function setSites(array $sites): void
    {
        config(['statamic.system.multisite' => count($sites) > 1]);

        Site::setSites($sites);
    }

    public function test_it_returns_locale_to_url_mapping_for_configured_sites(): void
    {
        $this->setSites([
            'en' => ['name' => 'English', 'url' => '/en', 'locale' => 'en_US'],
            'fr' => ['name' => 'French', 'url' => '/fr', 'locale' => 'fr_FR'],
        ]);

        $reader = new SiteLocaleReader();
        $result = $reader->getLocaleUrlMap();

        $this->assertSame([
            'en' => '/en',
            'fr' => '/fr',
        ], $result);
    }

    public function test_it_extracts_short_locale_from_full_locale(): void
    {
        $this->setSites([
            'de' => ['name' => 'German', 'url' => '/de', 'locale' => 'de_DE'],
            'es' => ['name' => 'Spanish', 'url' => '/es', 'locale' => 'es_ES'],
        ]);

        $reader = new SiteLocaleReader();
        $result = $reader->getLocaleUrlMap();

        $this->assertSame([
            'de' => '/de',
            'es' => '/es',
        ], $result);
    }

    public function test_it_works_with_prefix_based_multi_site_setups(): void
    {
        $this->setSites([
            'default' => ['name' => 'English', 'url' => '/', 'locale' => 'en_US'],
            'fr' => ['name' => 'French', 'url' => '/fr/', 'locale' => 'fr_FR'],
            'de' => ['name' => 'German', 'url' => '/de/', 'locale' => 'de_DE'],
        ]);

        $reader = new SiteLocaleReader();
        $result = $reader->getLocaleUrlMap();

        $this->assertSame([
            'en' => '/',
            'fr' => '/fr',
            'de' => '/de',
        ], $result);
    }

    public function test_it_returns_all_configured_sites(): void
    {
        $this->setSites([
            'en' => ['name' => 'English', 'url' => '/en', 'locale' => 'en_US'],
            'fr' => ['name' => 'French', 'url' => '/fr', 'locale' => 'fr_FR'],
            'de' => ['name' => 'German', 'url' => '/de', 'locale' => 'de_DE'],
            'it' => ['name' => 'Italian', 'url' => '/it', 'locale' => 'it_IT'],
        ]);

        $reader = new SiteLocaleReader();
        $result = $reader->getLocaleUrlMap();

        $this->assertCount(4, $result);
        $this->assertArrayHasKey('en', $result);
        $this->assertArrayHasKey('fr', $result);
        $this->assertArrayHasKey('de', $result);
        $this->assertArrayHasKey('it', $result);
    }
}
