<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Noo\LocaleRedirect\LocaleRedirectMiddleware;
use Statamic\StaticCaching\UrlExcluder;

test('config is merged with defaults', function () {
    expect(config('statamic.locale-redirect'))
        ->toBeArray()
        ->toHaveKey('exclude')
        ->toHaveKey('only')
        ->toHaveKey('fallback');
});

test('default config has empty exclude array', function () {
    expect(config('statamic.locale-redirect.exclude'))->toBe([]);
});

test('default config has empty only array', function () {
    expect(config('statamic.locale-redirect.only'))->toBe([]);
});

test('default config has null fallback', function () {
    expect(config('statamic.locale-redirect.fallback'))->toBeNull();
});

test('default config does not redirect unprefixed urls', function () {
    expect(config('statamic.locale-redirect.redirect_unprefixed'))->toBeFalse();
});

test('root and migration checks run only through the statamic web group', function () {
    expect(Route::getMiddlewareGroups()['statamic.web'])->toContain(LocaleRedirectMiddleware::class);
});

test('config is publishable', function () {
    $publishGroups = ServiceProvider::$publishGroups;

    expect($publishGroups)->toHaveKey('locale-redirect');

    $paths = $publishGroups['locale-redirect'];
    $sourceFiles = array_keys($paths);
    $destinationFiles = array_values($paths);

    expect($paths)->toHaveCount(1);
    expect($sourceFiles[0])->toContain('config/locale-redirect.php');
    expect($destinationFiles[0])->toEndWith('statamic/locale-redirect.php');
});

test('the frontend catch-all handles root without an addon route', function () {
    $route = Route::getRoutes()->match(request()->create('/', 'GET'));

    expect($route->getName())->toBe('statamic.site');
    expect(Route::getRoutes()->getRoutesByMethod()['GET'])->not->toHaveKey('/');
});

test('the bound static cache excluder excludes the homepage', function () {
    config(['statamic.static_caching.strategy' => 'full']);
    expect(app(UrlExcluder::class)->isExcluded(url('/')))->toBeTrue();
});
