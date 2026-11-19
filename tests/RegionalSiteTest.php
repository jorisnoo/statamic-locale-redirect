<?php

use Statamic\Facades\Site;

beforeEach(function () {
    config(['statamic.system.multisite' => true, 'statamic.editions.pro' => true]);
    Site::setSites([
        'america' => ['url' => '/us', 'locale' => 'en_US'],
        'britain' => ['url' => '/uk', 'locale' => 'en_GB'],
        'france' => ['url' => '/fr', 'locale' => 'fr_FR'],
    ]);
});

test('exact regional matches take precedence; language ties keep the first site', function ($language, $target) {
    $this->get('/', ['Accept-Language' => $language])->assertRedirect($target);
})->with([
    ['en-US', '/us'], ['en-gb', '/uk'], ['en', '/us'], ['en-CA', '/us'], ['fr', '/fr'],
]);

test('site filters accept handles, full locales and whole languages', function ($key, $filters, $target) {
    config(["statamic.locale-redirect.{$key}" => $filters]);
    $this->get('/', ['Accept-Language' => 'en-GB, fr;q=0.8'])->assertRedirect($target);
})->with([
    ['exclude', ['en_GB'], '/us'], ['exclude', ['britain'], '/us'],
    ['exclude', ['en'], '/fr'], ['only', ['en-gb'], '/uk'], ['only', ['france'], '/fr'],
]);
