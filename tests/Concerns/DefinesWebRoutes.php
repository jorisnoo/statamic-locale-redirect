<?php

namespace Noo\LocaleRedirect\Tests\Concerns;

trait DefinesWebRoutes
{
    protected function defineWebRoutes($router): void
    {
        $router->get('/', fn () => response('homepage'));
        $router->get('/about', fn () => response('about'));
    }
}
