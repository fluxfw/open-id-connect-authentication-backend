<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

use FluxOpenIdConnectRestApi\Adapter\Api\OpenIdConnectRestApi;
use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxRestApi\Adapter\Cookie\CookieDto;
use FluxRestApi\Adapter\Header\DefaultHeaderKey;
use FluxRestApi\Adapter\Method\DefaultMethod;
use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxRestApi\Adapter\Route\Route;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Status\DefaultStatus;

class LoginRoute implements Route
{

    private function __construct(
        private readonly OpenIdConnectRestApi $open_id_connect_rest_api,
        private readonly CookieConfigDto $cookie_config
    ) {

    }


    public static function new(
        OpenIdConnectRestApi $open_id_connect_rest_api,
        CookieConfigDto $cookie_config
    ) : static {
        return new static(
            $open_id_connect_rest_api,
            $cookie_config
        );
    }


    public function getDocumentation() : ?RouteDocumentationDto
    {
        return RouteDocumentationDto::new(
            $this->getRoute(),
            $this->getMethod(),
            "Login user",
            null,
            null,
            null,
            null,
            [
                RouteResponseDocumentationDto::new(
                    null,
                    DefaultStatus::_302,
                    null,
                    "Redirect to provider authorization endpoint"
                )
            ]
        );
    }


    public function getMethod() : Method
    {
        return DefaultMethod::GET;
    }


    public function getRoute() : string
    {
        return "/login";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        [$encrypted_session, $authorize_url] = $this->open_id_connect_rest_api->login();

        return ServerResponseDto::new(
            null,
            DefaultStatus::_302,
            [
                DefaultHeaderKey::LOCATION->value => $authorize_url
            ],
            [
                CookieDto::new(
                    $this->cookie_config->name,
                    $encrypted_session,
                    $this->cookie_config->expires_in,
                    $this->cookie_config->path,
                    $this->cookie_config->domain,
                    $this->cookie_config->secure,
                    $this->cookie_config->http_only,
                    $this->cookie_config->same_site,
                    $this->cookie_config->priority
                )
            ]
        );
    }
}
