<?php

namespace FluxOpenIdConnectRestApi\Adapter\OpenId;

use FluxOpenIdConnectRestApi\Adapter\Provider\ProviderConfigDto;
use FluxOpenIdConnectRestApi\Adapter\Route\RouteConfigDto;
use FluxOpenIdConnectRestApi\Adapter\SessionCrypt\PlainSessionCrypt;
use FluxOpenIdConnectRestApi\Service\OpenIdConnect\Port\OpenIdConnectService;
use FluxOpenIdConnectRestApi\Service\Request\Port\RequestService;
use FluxRestApi\Adapter\Api\RestApi;

class OpenIdConfigDto
{

    private function __construct(
        public readonly ProviderConfigDto $provider_config,
        public readonly ?string $authorization_endpoint,
        public readonly ?string $token_endpoint,
        public readonly ?string $user_info_endpoint
    ) {

    }


    public static function new(
        ProviderConfigDto $provider_config,
        ?string $authorization_endpoint = null,
        ?string $token_endpoint = null,
        ?string $user_info_endpoint = null
    ) : static {
        return new static(
            $provider_config,
            $authorization_endpoint,
            $token_endpoint,
            $user_info_endpoint
        );
    }


    public static function newFromProvider(
        ProviderConfigDto $provider_config
    ) : static {
        return OpenIdConnectService::new(
            static::new(
                $provider_config
            ),
            RouteConfigDto::new(),
            PlainSessionCrypt::new(),
            RequestService::new(
                RestApi::new()
            )
        )
            ->getOpenIdConfig();
    }
}
