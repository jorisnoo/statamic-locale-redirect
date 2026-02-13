<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Noo\LocaleRedirect\LocaleRedirectController;

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

test('config is publishable', function () {
    $publishGroups = ServiceProvider::$publishGroups;

    expect($publishGroups)->toHaveKey('statamic-locale-redirect');

    $paths = $publishGroups['statamic-locale-redirect'];
    $sourceFiles = array_keys($paths);
    $destinationFiles = array_values($paths);

    expect($paths)->toHaveCount(1);
    expect($sourceFiles[0])->toContain('config/locale-redirect.php');
    expect($destinationFiles[0])->toEndWith('statamic/locale-redirect.php');
});

test('root route is registered', function () {
    $route = Route::getRoutes()->match(
        request()->create('/', 'GET')
    );

    expect($route)->not->toBeNull();
    expect($route->getActionName())->toBe(LocaleRedirectController::class);
});
