<?php

namespace Noo\LocaleRedirect\Tests\Concerns;

trait DefinesWebRoutes
{
    protected function defineWebRoutes($router): void
    {
        $router->get('/about', fn () => response('about'));
    }
}
