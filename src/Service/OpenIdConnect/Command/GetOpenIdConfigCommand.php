<?php

namespace FluxOpenIdConnectRestApi\Service\OpenIdConnect\Command;

use FluxOpenIdConnectRestApi\Adapter\OpenId\OpenIdConfigDto;
use FluxOpenIdConnectRestApi\Adapter\Provider\ProviderConfigDto;
use FluxOpenIdConnectRestApi\Service\Request\Port\RequestService;

class GetOpenIdConfigCommand
{

    private function __construct(
        private readonly RequestService $request_service
    ) {

    }


    public static function new(
        RequestService $request_service
    ) : static {
        return new static(
            $request_service
        );
    }


    public function getOpenIdConfig(ProviderConfigDto $provider_config) : OpenIdConfigDto
    {
        $config = $this->request_service->request(
            $provider_config->url . "/.well-known/openid-configuration",
            null,
            null,
            null,
            $provider_config->trust_self_signed_certificate
        );

        return OpenIdConfigDto::new(
            $provider_config,
            $this->mapToProviderUrlProtocol(
                $config["authorization_endpoint"] ?? null,
                $provider_config->url
            ),
            $this->mapToProviderUrlProtocol(
                $config["token_endpoint"] ?? null,
                $provider_config->url
            ),
            $this->mapToProviderUrlProtocol(
                $config["userinfo_endpoint"] ?? null,
                $provider_config->url
            )
        );
    }


    private function mapToProviderUrlProtocol(?string $url, string $provider_url) : ?string
    {
        if (empty($url)) {
            return $url;
        }

        $matches = [];
        preg_match("/^https?:\/\//", $provider_url, $matches);
        if (empty($matches)) {
            return $url;
        }

        return preg_replace("/^https?:\/\//", $matches[0], $url);
    }
}
