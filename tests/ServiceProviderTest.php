<?php

use Illuminate\Support\ServiceProvider;

test('config is merged with defaults', function () {
    expect(config('locale-redirect'))
        ->toBeArray()
        ->toHaveKey('exclude')
        ->toHaveKey('only');
});

test('default config has empty exclude array', function () {
    expect(config('locale-redirect.exclude'))->toBe([]);
});

test('default config has empty only array', function () {
    expect(config('locale-redirect.only'))->toBe([]);
});

test('config is publishable', function () {
    $publishGroups = ServiceProvider::$publishGroups;

    expect($publishGroups)->toHaveKey('locale-redirect-config');

    $paths = $publishGroups['locale-redirect-config'];
    $sourceFiles = array_keys($paths);
    $destinationFiles = array_values($paths);

    expect($paths)->toHaveCount(1);
    expect($sourceFiles[0])->toContain('config/locale-redirect.php');
    expect($destinationFiles[0])->toEndWith('locale-redirect.php');
});
