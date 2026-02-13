<?php

use Noo\LocaleRedirect\SiteLocaleReader;
use Statamic\Facades\Site;

function setSites(array $sites): void
{
    config(['statamic.system.multisite' => count($sites) > 1]);

    Site::setSites($sites);
}

test('it returns locale to url mapping for configured sites', function () {
    setSites([
        'en' => ['name' => 'English', 'url' => '/en', 'locale' => 'en_US'],
        'fr' => ['name' => 'French', 'url' => '/fr', 'locale' => 'fr_FR'],
    ]);

    $reader = new SiteLocaleReader();
    $result = $reader->getLocaleUrlMap();

    expect($result)->toBe([
        'en' => '/en',
        'fr' => '/fr',
    ]);
});

test('it extracts short locale from full locale', function () {
    setSites([
        'de' => ['name' => 'German', 'url' => '/de', 'locale' => 'de_DE'],
        'es' => ['name' => 'Spanish', 'url' => '/es', 'locale' => 'es_ES'],
    ]);

    $reader = new SiteLocaleReader();
    $result = $reader->getLocaleUrlMap();

    expect($result)->toBe([
        'de' => '/de',
        'es' => '/es',
    ]);
});

test('it works with prefix based multi site setups', function () {
    setSites([
        'default' => ['name' => 'English', 'url' => '/', 'locale' => 'en_US'],
        'fr' => ['name' => 'French', 'url' => '/fr/', 'locale' => 'fr_FR'],
        'de' => ['name' => 'German', 'url' => '/de/', 'locale' => 'de_DE'],
    ]);

    $reader = new SiteLocaleReader();
    $result = $reader->getLocaleUrlMap();

    expect($result)->toBe([
        'en' => '/',
        'fr' => '/fr',
        'de' => '/de',
    ]);
});

test('it returns all configured sites', function () {
    setSites([
        'en' => ['name' => 'English', 'url' => '/en', 'locale' => 'en_US'],
        'fr' => ['name' => 'French', 'url' => '/fr', 'locale' => 'fr_FR'],
        'de' => ['name' => 'German', 'url' => '/de', 'locale' => 'de_DE'],
        'it' => ['name' => 'Italian', 'url' => '/it', 'locale' => 'it_IT'],
    ]);

    $reader = new SiteLocaleReader();
    $result = $reader->getLocaleUrlMap();

    expect($result)->toHaveCount(4)
        ->toHaveKey('en')
        ->toHaveKey('fr')
        ->toHaveKey('de')
        ->toHaveKey('it');
});
