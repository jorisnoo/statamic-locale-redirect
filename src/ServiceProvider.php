<?php

namespace Noo\LocaleRedirect;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/locale-redirect.php', 'statamic.locale-redirect');

        $this->publishes([
            __DIR__ . '/../config/locale-redirect.php' => config_path('statamic/locale-redirect.php'),
        ], 'locale-redirect');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->excludeFromStaticCache();
    }

    private function excludeFromStaticCache(): void
    {
        $exclude = config('statamic.static_caching.exclude', []);

        if (! in_array('/', $exclude)) {
            $exclude[] = '/';
            config(['statamic.static_caching.exclude' => $exclude]);
        }
    }
}
