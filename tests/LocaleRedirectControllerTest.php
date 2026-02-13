<?php

use Statamic\Facades\Site;

uses(Noo\LocaleRedirect\Tests\Concerns\DefinesWebRoutes::class);

beforeEach(function () {
    config(['statamic.system.multisite' => true]);
    config(['statamic.editions.pro' => true]);

    Site::setSites([
        'en' => ['name' => 'English', 'url' => '/en', 'locale' => 'en_US'],
        'fr' => ['name' => 'French', 'url' => '/fr', 'locale' => 'fr_FR'],
        'de' => ['name' => 'German', 'url' => '/de', 'locale' => 'de_DE'],
    ]);
});

test('it redirects to matched locale home url', function () {
    $this->get('/', ['Accept-Language' => 'fr'])
        ->assertRedirect('/fr')
        ->assertStatus(302);
});

test('it respects browser language priority', function () {
    $this->get('/', ['Accept-Language' => 'en-US,en;q=0.9,fr;q=0.8'])
        ->assertRedirect('/en')
        ->assertStatus(302);
});

test('it redirects to default site when no locale matches', function () {
    $this->get('/', ['Accept-Language' => 'ja,zh;q=0.9'])
        ->assertRedirect('/en')
        ->assertStatus(302);
});

test('it redirects bots to default site', function () {
    $this->get('/', [
        'Accept-Language' => 'fr',
        'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    ])->assertRedirect('/en')
        ->assertStatus(302);
});

test('it redirects crawlers to default site', function () {
    $this->get('/', [
        'Accept-Language' => 'fr',
        'User-Agent' => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
    ])->assertRedirect('/en')
        ->assertStatus(302);
});

test('it redirects requests without user agent to default site', function () {
    $this->get('/', [
        'Accept-Language' => 'fr',
        'User-Agent' => '',
    ])->assertRedirect('/en')
        ->assertStatus(302);
});

test('it does not intercept non home routes', function () {
    $this->get('/about', ['Accept-Language' => 'fr'])
        ->assertOk()
        ->assertSee('about');
});

test('it handles partial locale match', function () {
    $this->get('/', ['Accept-Language' => 'de-DE,de;q=0.9'])
        ->assertRedirect('/de')
        ->assertStatus(302);
});

test('excluded locales are skipped during matching', function () {
    config(['statamic.locale-redirect.exclude' => ['fr']]);

    $this->get('/', ['Accept-Language' => 'fr,en;q=0.8'])
        ->assertRedirect('/en')
        ->assertStatus(302);
});

test('excluded locale with no fallback match redirects to default site', function () {
    config(['statamic.locale-redirect.exclude' => ['fr']]);

    $this->get('/', ['Accept-Language' => 'fr'])
        ->assertRedirect('/en')
        ->assertStatus(302);
});

test('no locales excluded by default', function () {
    $this->get('/', ['Accept-Language' => 'fr'])
        ->assertRedirect('/fr')
        ->assertStatus(302);
});

test('only config restricts matching to specified locales', function () {
    config(['statamic.locale-redirect.only' => ['en', 'fr']]);

    $this->get('/', ['Accept-Language' => 'de,fr;q=0.8'])
        ->assertRedirect('/fr')
        ->assertStatus(302);
});

test('only config with no match redirects to default site', function () {
    config(['statamic.locale-redirect.only' => ['en']]);

    $this->get('/', ['Accept-Language' => 'fr,de;q=0.9'])
        ->assertRedirect('/en')
        ->assertStatus(302);
});

test('only takes precedence over exclude', function () {
    config([
        'statamic.locale-redirect.only' => ['en', 'fr'],
        'statamic.locale-redirect.exclude' => ['fr'],
    ]);

    $this->get('/', ['Accept-Language' => 'fr'])
        ->assertRedirect('/fr')
        ->assertStatus(302);
});

test('all locales considered when only not configured', function () {
    config(['statamic.locale-redirect.only' => []]);

    $this->get('/', ['Accept-Language' => 'de'])
        ->assertRedirect('/de')
        ->assertStatus(302);
});

test('fallback redirects to configured url when set', function () {
    config(['statamic.locale-redirect.fallback' => '/custom-landing']);

    $this->get('/', ['Accept-Language' => 'ja'])
        ->assertRedirect('/custom-landing')
        ->assertStatus(302);
});

test('bot redirects to configured fallback url when set', function () {
    config(['statamic.locale-redirect.fallback' => '/custom-landing']);

    $this->get('/', [
        'Accept-Language' => 'fr',
        'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    ])->assertRedirect('/custom-landing')
        ->assertStatus(302);
});
