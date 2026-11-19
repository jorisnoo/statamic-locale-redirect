<?php

namespace Noo\LocaleRedirect;

use Illuminate\Http\Request;

final class RedirectTarget
{
    public static function absolute(string $url): string
    {
        // Site paths are relative to the installation, not to the incoming Host.
        return parse_url($url, PHP_URL_HOST) === null
            ? rtrim(config('app.url'), '/') . '/' . ltrim($url, '/')
            : $url;
    }

    public static function sameOrigin(Request $request, string $url): bool
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');

        return $scheme === $request->getScheme()
            && strcasecmp($parts['host'] ?? '', $request->getHost()) === 0
            && ($parts['port'] ?? ($scheme === 'https' ? 443 : 80)) === $request->getPort();
    }

    public static function isCurrent(Request $request, string $url): bool
    {
        $parts = parse_url($url);
        $port = $parts['port'] ?? null;
        $defaultPort = strtolower($parts['scheme'] ?? '') === 'https' ? 443 : 80;
        $requestDefaultPort = $request->isSecure() ? 443 : 80;

        // A proxy may terminate TLS. Switching schemes/default ports alone must not loop.
        return strcasecmp($parts['host'] ?? '', $request->getHost()) === 0
            && ($port === $defaultPort ? null : $port) === ($request->getPort() === $requestDefaultPort ? null : $request->getPort())
            && rtrim($parts['path'] ?? '', '/') === rtrim(parse_url($request->url(), PHP_URL_PATH) ?? '', '/');
    }

    public static function withQuery(Request $request, string $target): string
    {
        // Symfony's getQueryString() normalizes keys, order and escaping.
        $query = $request->server->get('QUERY_STRING', '');
        if ($query === '') {
            return $target;
        }

        [$url, $fragment] = array_pad(explode('#', $target, 2), 2, null);

        return $url . (str_contains($url, '?') ? '&' : '?') . $query . ($fragment === null ? '' : '#' . $fragment);
    }
}
