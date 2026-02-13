<?php

namespace Noordermeer\LocaleRedirect\Tests;

use Noordermeer\LocaleRedirect\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
