<?php

namespace Noo\LocaleRedirect;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $middlewareGroups = [
        'web' => [
            LocaleRedirectMiddleware::class,
        ],
    ];

    public function bootAddon(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/locale-redirect.php', 'statamic.locale-redirect');

        $this->publishes([
            __DIR__ . '/../config/locale-redirect.php' => config_path('statamic/locale-redirect.php'),
        ], 'statamic-locale-redirect');
    }
}
