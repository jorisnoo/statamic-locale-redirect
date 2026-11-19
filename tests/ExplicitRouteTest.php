<?php

namespace Noo\LocaleRedirect\Tests;

use Illuminate\Support\Facades\Route;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class ExplicitRouteTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected function defineWebRoutes($router): void
    {
        $router->get('/', fn () => response('app homepage'));
        Route::statamic('/about', fn () => response('custom Statamic page'));
    }

    public function test_explicit_routes_win_over_both_redirects(): void
    {
        config(['statamic.locale-redirect.redirect_unprefixed' => true]);
        Site::setSites(['en' => ['url' => '/en', 'locale' => 'en_US']]);
        Collection::make('pages')->routes('{slug}')->sites(['en'])->save();
        Entry::make()->collection('pages')->locale('en')->slug('about')->save();

        $this->get('/')->assertOk()->assertSee('app homepage');
        $this->get('/about')->assertOk()->assertSee('custom Statamic page');
    }
}
