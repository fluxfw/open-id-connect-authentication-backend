<?php

namespace FluxOpenIdConnectRestApi\Service\OpenIdConnect\Command;

use FluxOpenIdConnectRestApi\Adapter\Route\RouteConfigDto;

class LogoutCommand
{

    private function __construct(
        private readonly RouteConfigDto $route_config
    ) {

    }


    public static function new(
        RouteConfigDto $route_config
    ) : static {
        return new static(
            $route_config
        );
    }


    public function logout() : string
    {
        return $this->route_config->after_logout_url;
    }
}
