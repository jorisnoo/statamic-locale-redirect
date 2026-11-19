<?php

namespace Noo\LocaleRedirect\Tests;

use Illuminate\Support\Facades\Route;
use Noo\LocaleRedirect\LocaleRedirectMiddleware;

class HeadlessRouteTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('statamic.routes.enabled', false);
    }

    protected function defineWebRoutes($router): void
    {
        $router->get('/', fn () => response('headless root'));
    }

    public function test_disabled_statamic_frontend_does_not_claim_root(): void
    {
        $this->get('/')->assertOk()->assertSee('headless root');
        $this->assertNotContains(LocaleRedirectMiddleware::class, Route::getMiddlewareGroups()['statamic.web']);
    }
}
