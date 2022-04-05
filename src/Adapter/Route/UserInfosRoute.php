<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxOpenIdConnectRestApi\Libs\FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Body\JsonBodyDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Body\TextBodyDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Method\DefaultMethod;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Method\Method;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Request\RequestDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Response\ResponseDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Route\Route;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Status\DefaultStatus;

class UserInfosRoute implements Route
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
        return "/userinfos";
    }


    public function handle(RequestDto $request) : ?ResponseDto
    {
        $user_infos = $this->open_id_connect_api->getUserInfos(
            $request->getCookie(
                $this->cookie_config->name
            )
        );

        if ($user_infos !== null) {
            return ResponseDto::new(
                JsonBodyDto::new(
                    $user_infos
                )
            );
        } else {
            return ResponseDto::new(
                TextBodyDto::new(
                    "Authorization needed"
                ),
                DefaultStatus::_401
            );
        }
    }
}
