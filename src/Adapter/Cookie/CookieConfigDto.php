<?php

namespace FluxOpenIdConnectRestApi\Adapter\Cookie;

use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Cookie\Priority\CookiePriority;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Cookie\Priority\DefaultCookiePriority;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Cookie\SameSite\CookieSameSite;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Cookie\SameSite\DefaultCookieSameSite;

class CookieConfigDto
{

    private function __construct(
        public readonly string $name,
        public readonly ?int $expires_in,
        public readonly string $path,
        public readonly string $domain,
        public readonly bool $secure,
        public readonly bool $http_only,
        public readonly CookieSameSite $same_site,
        public readonly CookiePriority $priority
    ) {

    }


    public static function new(
        ?string $name = null,
        ?int $expires_in = null,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        ?bool $http_only = null,
        ?CookieSameSite $same_site = null,
        ?CookiePriority $priority = null
    ) : static {
        return new static(
            $name ?? "auth",
            $expires_in,
            $path ?? "/",
            $domain ?? "",
            $secure ?? true,
            $http_only ?? true,
            $same_site ?? DefaultCookieSameSite::LAX,
            $priority ?? DefaultCookiePriority::MEDIUM
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_NAME"] ?? null,
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_EXPIRES_IN"] ?? null,
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_PATH"] ?? null,
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_DOMAIN"] ?? null,
            ($secure = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_SECURE"] ?? null) !== null ? in_array($secure, ["true", "1"]) : null,
            ($http_only = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_HTTP_ONLY"] ?? null) !== null ? in_array($http_only, ["true", "1"]) : null,
            ($same_site = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_SAME_SITE"] ?? null) !== null ? DefaultCookieSameSite::from($same_site) : null,
            ($priority = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_COOKIE_PRIORITY"] ?? null) !== null ? DefaultCookiePriority::from($priority) : null
        );
    }
}
