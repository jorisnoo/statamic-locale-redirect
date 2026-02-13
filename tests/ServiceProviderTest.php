<?php

namespace Noo\LocaleRedirect\Tests;

class ServiceProviderTest extends TestCase
{
    public function test_config_is_merged_with_defaults(): void
    {
        $this->assertIsArray(config('locale-redirect'));
        $this->assertArrayHasKey('exclude', config('locale-redirect'));
        $this->assertArrayHasKey('only', config('locale-redirect'));
    }

    public function test_default_config_has_empty_exclude_array(): void
    {
        $this->assertSame([], config('locale-redirect.exclude'));
    }

    public function test_default_config_has_empty_only_array(): void
    {
        $this->assertSame([], config('locale-redirect.only'));
    }

    public function test_config_is_publishable(): void
    {
        $publishGroups = \Illuminate\Support\ServiceProvider::$publishGroups;

        $this->assertArrayHasKey('locale-redirect-config', $publishGroups);

        $paths = $publishGroups['locale-redirect-config'];
        $sourceFiles = array_keys($paths);
        $destinationFiles = array_values($paths);

        $this->assertCount(1, $paths);
        $this->assertStringContainsString('config/locale-redirect.php', $sourceFiles[0]);
        $this->assertStringEndsWith('locale-redirect.php', $destinationFiles[0]);
    }
}
