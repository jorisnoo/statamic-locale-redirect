<?php

namespace Noordermeer\LocaleRedirect;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $routes = [
        'web' => __DIR__ . '/../routes/web.php',
    ];

    public function bootAddon(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/locale-redirect.php', 'locale-redirect');

        $this->publishes([
            __DIR__ . '/../config/locale-redirect.php' => config_path('locale-redirect.php'),
        ], 'locale-redirect-config');
    }
}
