<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

class RouteConfigDto
{

    private function __construct(
        public readonly string $after_login_url,
        public readonly string $after_logout_url
    ) {

    }


    public static function new(
        ?string $after_login_url = null,
        ?string $after_logout_url = null
    ) : static {
        return new static(
            $after_login_url ?? "/",
            $after_logout_url ?? "/"
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_ROUTE_AFTER_LOGIN_URL"] ?? null,
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_ROUTE_AFTER_LOGOUT_URL"] ?? null
        );
    }
}
