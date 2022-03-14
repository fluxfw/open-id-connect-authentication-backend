<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

use FluxOpenIdConnectRestApi\Adapter\Config\CookieConfigDto;
use FluxOpenIdConnectRestApi\Libs\FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Cookie\CookieDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Header\DefaultHeader;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Method\DefaultMethod;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Method\Method;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Libs\FluxRestBaseApi\Status\DefaultStatus;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Request\RequestDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Response\ResponseDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Route\Route;

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


    public function getDocuRequestBodyTypes() : ?array
    {
        return null;
    }


    public function getDocuRequestQueryParams() : ?array
    {
        return null;
    }


    public function getMethod() : Method
    {
        return DefaultMethod::GET;
    }


    public function getRoute() : string
    {
        return "/logout";
    }


    public function handle(RequestDto $request) : ?ResponseDto
    {
        return ResponseDto::new(
            null,
            DefaultStatus::_302,
            [
                DefaultHeader::LOCATION->value => $this->open_id_connect_api->logout()
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
