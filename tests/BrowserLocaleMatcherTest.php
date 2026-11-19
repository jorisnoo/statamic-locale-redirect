<?php

use Noo\LocaleRedirect\BrowserLocaleMatcher;

test('it matches exact browser locale to site locale', function () {
    $matcher = new BrowserLocaleMatcher();

    $result = $matcher->match(
        availableLocales: ['en', 'fr'],
        acceptLanguageHeader: 'fr'
    );

    expect($result)->toBe('fr');
});

test('it respects quality values and returns highest priority match', function () {
    $matcher = new BrowserLocaleMatcher();

    $result = $matcher->match(
        availableLocales: ['en', 'fr'],
        acceptLanguageHeader: 'en-US,en;q=0.9,fr;q=0.8'
    );

    expect($result)->toBe('en');
});

test('it handles partial match when browser sends full locale', function () {
    $matcher = new BrowserLocaleMatcher();

    $result = $matcher->match(
        availableLocales: ['en', 'fr', 'de'],
        acceptLanguageHeader: 'de-DE,de;q=0.9'
    );

    expect($result)->toBe('de');
});

test('it returns null when no locale matches', function () {
    $matcher = new BrowserLocaleMatcher();

    $result = $matcher->match(
        availableLocales: ['en', 'fr'],
        acceptLanguageHeader: 'ja,zh;q=0.9'
    );

    expect($result)->toBeNull();
});

test('it returns null for empty accept language header', function () {
    $matcher = new BrowserLocaleMatcher();

    $result = $matcher->match(
        availableLocales: ['en', 'fr'],
        acceptLanguageHeader: ''
    );

    expect($result)->toBeNull();
});

test('it matches lower priority locale when higher is unavailable', function () {
    $matcher = new BrowserLocaleMatcher();

    $result = $matcher->match(
        availableLocales: ['fr', 'de'],
        acceptLanguageHeader: 'en-US,en;q=0.9,fr;q=0.8'
    );

    expect($result)->toBe('fr');
});

test('quality weights are case insensitive, bounded and must be positive', function ($header, $match) {
    expect((new BrowserLocaleMatcher())->match(['en', 'fr'], $header))->toBe($match);
})->with([
    ['de, fr;q=0', null],
    ['fr;Q=0.1, en;q=0.9', 'en'],
    ['fr;q=-0.1', null],
    ['en;q=1, fr;q=2', 'en'],
]);
