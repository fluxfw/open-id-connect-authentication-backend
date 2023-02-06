<?php

namespace FluxOpenIdConnectRestApi\Adapter\Api;

use FluxOpenIdConnectRestApi\Adapter\OpenId\OpenIdConfigDto;
use FluxOpenIdConnectRestApi\Adapter\Provider\ProviderConfigDto;
use FluxOpenIdConnectRestApi\Adapter\Route\RouteConfigDto;
use FluxOpenIdConnectRestApi\Adapter\SessionCrypt\PlainSessionCrypt;
use FluxOpenIdConnectRestApi\Adapter\SessionCrypt\SecretSessionCrypt;
use FluxOpenIdConnectRestApi\Adapter\SessionCrypt\SessionCrypt;
use FluxOpenIdConnectRestApi\Adapter\SessionCrypt\SessionCryptConfigDto;

class OpenIdConnectRestApiConfigDto
{

    private function __construct(
        public readonly OpenIdConfigDto $open_id_config,
        public readonly RouteConfigDto $route_config,
        public readonly SessionCrypt $session_crypt
    ) {

    }


    public static function new(
        OpenIdConfigDto $open_id_config,
        RouteConfigDto $route_config,
        SessionCrypt $session_crypt
    ) : static {
        return new static(
            $open_id_config,
            $route_config,
            $session_crypt
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            OpenIdConfigDto::newFromProvider(
                ProviderConfigDto::newFromEnv()
            ),
            RouteConfigDto::newFromEnv(),
            !(($plain = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_SESSION_CRYPT_PLAIN"] ?? null) !== null && in_array($plain, ["true", "1"])) ? SecretSessionCrypt::new(
                SessionCryptConfigDto::newFromEnv()
            ) : PlainSessionCrypt::new()
        );
    }
}
