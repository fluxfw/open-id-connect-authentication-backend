<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxOpenIdConnectRestApi\Libs\FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Cookie\CookieDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Header\DefaultHeaderKey;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Method\DefaultMethod;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Method\Method;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Route\Route;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Status\DefaultStatus;

class LogoutRoute implements Route
{

    private function __construct(
        private readonly OpenIdConnectApi $open_id_connect_api,
        private readonly CookieConfigDto $cookie_config
    ) {

    }


    public static function new(
        OpenIdConnectApi $open_id_connect_api,
        CookieConfigDto $cookie_config
    ) : static {
        return new static(
            $open_id_connect_api,
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
                DefaultHeaderKey::LOCATION->value => $this->open_id_connect_api->logout()
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
