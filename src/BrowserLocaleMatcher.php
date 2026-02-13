<?php

namespace Noo\LocaleRedirect;

use koenster\PHPLanguageDetection\BrowserLocalization;

class BrowserLocaleMatcher
{
    /**
     * @param array<int, string> $availableLocales
     */
    public function match(array $availableLocales, string $acceptLanguageHeader): ?string
    {
        if ($acceptLanguageHeader === '' || $availableLocales === []) {
            return null;
        }

        $browser = new BrowserLocalization();

        $browser->setAvailable($availableLocales)
            ->setDefault('')
            ->setPreferences($acceptLanguageHeader);

        $detected = $browser->detect();

        if ($detected === '') {
            return null;
        }

        return $detected;
    }
}
