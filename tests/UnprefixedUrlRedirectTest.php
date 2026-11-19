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

test('root site ownership takes precedence even when the default site has a matching entry', function () {
    Site::get('en')->set('url', '/');
    Entry::make()->collection('pages')->locale('en')->slug('about')
        ->data(['redirect' => '/root-site-page'])->save();
    $this->get('/about')->assertStatus(302)->assertRedirect('/root-site-page');
});

test('a default site still rooted at slash never redirects its pages to themselves', function () {
    Site::get('de')->set('url', '/');
    $entry = Entry::findByUri('/about', 'de');
    $entry->set('redirect', '/root-page')->save();
    $this->get('/about')->assertStatus(302)->assertRedirect('/root-page');
});

test('migration is restricted to the old origin including scheme and port', function ($otherOrigin) {
    $this->get($otherOrigin . '/about')->assertNotFound();
})->with(['http://other.test', 'https://localhost', 'http://localhost:8001']);

test('head requests retain repeated query pairs unmodified', function () {
    $this->head('/about?x=a%20b&x=c&flag')->assertStatus(301)
        ->assertHeader('Location', 'http://localhost/de/about?x=a%20b&x=c&flag');
});

test('options is not a migration or language negotiation request', function () {
    $this->options('/about')->assertNotFound();
    $this->options('/')->assertNotFound();
});

test('entry redirects retain their own target and status after migration', function () {
    $entry = Entry::findByUri('/about', 'de');
    $entry->set('redirect', ['url' => '/new?edition=2#today', 'status' => 302])->save();
    $this->get('/about?x=1&x=2')->assertStatus(301)->assertRedirect('/de/about?x=1&x=2');
    $this->get('/de/about?x=1&x=2')->assertStatus(302)->assertRedirect('/new?edition=2#today');
});

test('mounted applications migrate to the canonical prefix exactly once', function ($siteUrl) {
    config(['app.url' => 'http://localhost/install']);
    Site::get('de')->set('url', $siteUrl);
    $this->withServerVariables([
        'SCRIPT_NAME' => '/install/index.php',
        'SCRIPT_FILENAME' => '/srv/install/index.php',
    ])->get('http://localhost/install/about?x=1&x=2')->assertStatus(301)
        ->assertHeader('Location', 'http://localhost/install/de/about?x=1&x=2');
})->with(['/de', 'http://localhost/install/de']);
