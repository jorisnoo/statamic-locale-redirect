<?php

namespace Noo\LocaleRedirect;

use Illuminate\Routing\Router;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/locale-redirect.php', 'statamic.locale-redirect');

        $this->publishes([
            __DIR__ . '/../config/locale-redirect.php' => config_path('statamic/locale-redirect.php'),
        ], 'locale-redirect');

        if (config('statamic.routes.enabled')) {
            // Statamic 5 creates this group after addon boot; push also initializes it.
            $this->app->make(Router::class)->pushMiddlewareToGroup('statamic.web', LocaleRedirectMiddleware::class);
            $this->excludeFromStaticCache();
        }
    }

    private function excludeFromStaticCache(): void
    {
        $key = 'statamic.static_caching.exclude';
        if (! array_is_list(config($key, []))) {
            $key .= '.urls';
        }

        $urls = config($key, []);
        if (! in_array('/', $urls, true)) {
            config([$key => [...$urls, '/']]);
        }
    }
}
