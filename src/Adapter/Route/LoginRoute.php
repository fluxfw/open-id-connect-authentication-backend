<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxOpenIdConnectRestApi\Libs\FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Cookie\CookieDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Header\DefaultHeader;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Method\DefaultMethod;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Method\Method;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Request\RequestDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Response\ResponseDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Route\Route;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Status\DefaultStatus;

class LoginRoute implements Route
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
        return "/login";
    }


    public function handle(RequestDto $request) : ?ResponseDto
    {
        [$encrypted_session, $authorize_url] = $this->open_id_connect_api->login();

        return ResponseDto::new(
            null,
            DefaultStatus::_302,
            [
                DefaultHeader::LOCATION->value => $authorize_url
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
