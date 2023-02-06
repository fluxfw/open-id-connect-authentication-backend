<?php

namespace FluxOpenIdConnectRestApi\Adapter\Server;

use FluxOpenIdConnectRestApi\Adapter\Api\OpenIdConnectRestApi;
use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxOpenIdConnectRestApi\Adapter\Route\CallbackRoute;
use FluxOpenIdConnectRestApi\Adapter\Route\LoginRoute;
use FluxOpenIdConnectRestApi\Adapter\Route\LogoutRoute;
use FluxOpenIdConnectRestApi\Adapter\Route\UserInfosRoute;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;

class OpenIdConnectRestApiServerRouteCollector implements RouteCollector
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


    public function collectRoutes() : array
    {
        return [
            CallbackRoute::new(
                $this->open_id_connect_rest_api,
                $this->cookie_config
            ),
            LoginRoute::new(
                $this->open_id_connect_rest_api,
                $this->cookie_config
            ),
            LogoutRoute::new(
                $this->open_id_connect_rest_api,
                $this->cookie_config
            ),
            UserInfosRoute::new(
                $this->open_id_connect_rest_api,
                $this->cookie_config
            )
        ];
    }
}
