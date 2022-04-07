<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxOpenIdConnectRestApi\Libs\FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Body\TextBodyDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Method\DefaultMethod;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Method\Method;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Route\Route;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Status\DefaultStatus;

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


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        $user_infos = $this->open_id_connect_api->getUserInfos(
            $request->getCookie(
                $this->cookie_config->name
            )
        );

        if ($user_infos !== null) {
            return ServerResponseDto::new(
                JsonBodyDto::new(
                    $user_infos
                )
            );
        } else {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Authorization needed"
                ),
                DefaultStatus::_401
            );
        }
    }
}
