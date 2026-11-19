<?php

use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

uses(PreventsSavingStacheItemsToDisk::class);

beforeEach(function () {
    config(['statamic.system.multisite' => true]);
    config(['statamic.editions.pro' => true]);
    config(['statamic.locale-redirect.redirect_unprefixed' => true]);

    Site::setSites([
        'de' => ['name' => 'Deutsch', 'url' => '/de', 'locale' => 'de_CH'],
        'en' => ['name' => 'English', 'url' => '/en', 'locale' => 'en_US'],
    ]);

    Collection::make('pages')->routes('{slug}')->sites(['de', 'en'])->save();

    Entry::make()->collection('pages')->locale('de')->slug('about')->data(['title' => 'About'])->save();
    Entry::make()->collection('pages')->locale('de')->slug('draft')->data(['title' => 'Draft'])->published(false)->save();
});

test('it redirects an unprefixed url to the default site entry', function () {
    $this->get('/about')
        ->assertRedirect('/de/about')
        ->assertStatus(301);
});

test('it preserves query parameters', function () {
    $this->get('/about?utm_source=newsletter')
        ->assertRedirect('/de/about?utm_source=newsletter');
});

test('it does nothing when disabled', function () {
    config(['statamic.locale-redirect.redirect_unprefixed' => false]);

    expect($this->get('/about')->status())->not->toBe(301);
});

test('it does not redirect unpublished entries', function () {
    $this->get('/draft')->assertNotFound();
});

test('it lets unknown urls fall through', function () {
    $this->get('/missing')->assertNotFound();
});

test('it leaves prefixed urls alone', function () {
    expect($this->get('/de/about')->status())->not->toBe(301);
    $this->get('/de/missing')->assertNotFound();
});

test('it only redirects safe methods', function () {
    expect($this->post('/about')->status())->not->toBe(301);
});

test('it leaves the root to the locale redirect', function () {
    $this->get('/', ['Accept-Language' => 'en'])
        ->assertRedirect('/en')
        ->assertStatus(302);
});
