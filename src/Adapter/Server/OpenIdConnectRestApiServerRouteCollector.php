<?php

namespace FluxOpenIdConnectRestApi\Adapter\Server;

use FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxOpenIdConnectRestApi\Adapter\Route\CallbackRoute;
use FluxOpenIdConnectRestApi\Adapter\Route\LoginRoute;
use FluxOpenIdConnectRestApi\Adapter\Route\LogoutRoute;
use FluxOpenIdConnectRestApi\Adapter\Route\UserInfosRoute;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;

class OpenIdConnectRestApiServerRouteCollector implements RouteCollector
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


    public function collectRoutes() : array
    {
        return [
            CallbackRoute::new(
                $this->open_id_connect_api,
                $this->cookie_config
            ),
            LoginRoute::new(
                $this->open_id_connect_api,
                $this->cookie_config
            ),
            LogoutRoute::new(
                $this->open_id_connect_api,
                $this->cookie_config
            ),
            UserInfosRoute::new(
                $this->open_id_connect_api,
                $this->cookie_config
            )
        ];
    }
}
