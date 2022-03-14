<?php

namespace FluxOpenIdConnectRestApi\Adapter\Server;

use FluxOpenIdConnectRestApi\Adapter\Config\CookieConfigDto;
use FluxOpenIdConnectRestApi\Libs\FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApiConfigDto;

class OpenIdConnectRestApiServerConfigDto
{

    private function __construct(
        public readonly OpenIdConnectApiConfigDto $open_id_connect_api_config,
        public readonly CookieConfigDto $cookie_config,
        public readonly ?string $https_cert,
        public readonly ?string $https_key,
        public readonly string $listen,
        public readonly int $port
    ) {

    }


    public static function new(
        OpenIdConnectApiConfigDto $open_id_connect_api_config,
        CookieConfigDto $cookie_config,
        ?string $https_cert = null,
        ?string $https_key = null,
        ?string $listen = null,
        ?int $port = null
    ) : static {
        return new static(
            $open_id_connect_api_config,
            $cookie_config,
            $https_cert,
            $https_key,
            $listen ?? "0.0.0.0",
            $port ?? 9501
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            OpenIdConnectApiConfigDto::newFromEnv(),
            CookieConfigDto::newFromEnv(),
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_SERVER_HTTPS_CERT"] ?? null,
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_SERVER_HTTPS_KEY"] ?? null,
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_SERVER_LISTEN"] ?? null,
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_SERVER_PORT"] ?? null
        );
    }
}
