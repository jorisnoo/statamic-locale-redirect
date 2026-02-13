<?php

namespace Noordermeer\LocaleRedirect\Tests;

use Statamic\Facades\Site;

class LocaleRedirectMiddlewareTest extends TestCase
{
    private function setSites(array $sites): void
    {
        config(['statamic.system.multisite' => count($sites) > 1]);
        config(['statamic.editions.pro' => true]);

        Site::setSites($sites);
    }

    protected function defineWebRoutes($router): void
    {
        $router->get('/', fn () => response('homepage'));
        $router->get('/about', fn () => response('about'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setSites([
            'en' => ['name' => 'English', 'url' => '/en', 'locale' => 'en_US'],
            'fr' => ['name' => 'French', 'url' => '/fr', 'locale' => 'fr_FR'],
            'de' => ['name' => 'German', 'url' => '/de', 'locale' => 'de_DE'],
        ]);
    }

    public function test_it_redirects_to_matched_locale_home_url(): void
    {
        $this->get('/', ['Accept-Language' => 'fr'])
            ->assertRedirect('/fr')
            ->assertStatus(302);
    }

    public function test_it_respects_browser_language_priority(): void
    {
        $this->get('/', ['Accept-Language' => 'en-US,en;q=0.9,fr;q=0.8'])
            ->assertRedirect('/en')
            ->assertStatus(302);
    }

    public function test_it_does_not_redirect_when_no_locale_matches(): void
    {
        $this->get('/', ['Accept-Language' => 'ja,zh;q=0.9'])
            ->assertOk();
    }

    public function test_it_does_not_redirect_bots(): void
    {
        $this->get('/', [
            'Accept-Language' => 'fr',
            'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ])->assertOk();
    }

    public function test_it_does_not_redirect_crawlers(): void
    {
        $this->get('/', [
            'Accept-Language' => 'fr',
            'User-Agent' => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
        ])->assertOk();
    }

    public function test_it_does_not_redirect_requests_without_user_agent(): void
    {
        $this->get('/', [
            'Accept-Language' => 'fr',
            'User-Agent' => '',
        ])->assertOk();
    }

    public function test_it_does_not_intercept_non_home_routes(): void
    {
        $this->get('/about', ['Accept-Language' => 'fr'])
            ->assertOk()
            ->assertSee('about');
    }

    public function test_it_handles_partial_locale_match(): void
    {
        $this->get('/', ['Accept-Language' => 'de-DE,de;q=0.9'])
            ->assertRedirect('/de')
            ->assertStatus(302);
    }

    public function test_excluded_locales_are_skipped_during_matching(): void
    {
        config(['locale-redirect.exclude' => ['fr']]);

        $this->get('/', ['Accept-Language' => 'fr,en;q=0.8'])
            ->assertRedirect('/en')
            ->assertStatus(302);
    }

    public function test_excluded_locale_with_no_fallback_does_not_redirect(): void
    {
        config(['locale-redirect.exclude' => ['fr']]);

        $this->get('/', ['Accept-Language' => 'fr'])
            ->assertOk();
    }

    public function test_no_locales_excluded_by_default(): void
    {
        $this->get('/', ['Accept-Language' => 'fr'])
            ->assertRedirect('/fr')
            ->assertStatus(302);
    }
}
