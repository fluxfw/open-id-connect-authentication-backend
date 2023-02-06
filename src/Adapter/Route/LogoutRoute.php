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

class LogoutRoute implements Route
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
            "Logout user",
            null,
            null,
            null,
            null,
            [
                RouteResponseDocumentationDto::new(
                    null,
                    DefaultStatus::_302,
                    null,
                    "Redirect to after logout url"
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
        return "/logout";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        return ServerResponseDto::new(
            null,
            DefaultStatus::_302,
            [
                DefaultHeaderKey::LOCATION->value => $this->open_id_connect_rest_api->logout()
            ],
            [
                CookieDto::new(
                    $this->cookie_config->name,
                    null,
                    null,
                    $this->cookie_config->path,
                    $this->cookie_config->domain
                )
            ]
        );
    }
}
