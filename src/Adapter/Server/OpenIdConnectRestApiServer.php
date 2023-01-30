<?php

namespace FluxOpenIdConnectRestApi\Adapter\Server;

use FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxRestApi\Adapter\Api\RestApi;
use FluxRestApi\Adapter\Route\Collector\RouteCollector;
use FluxRestApi\Adapter\Server\SwooleServerConfigDto;

class OpenIdConnectRestApiServer
{

    private function __construct(
        private readonly RestApi $rest_api,
        private readonly RouteCollector $route_collector,
        private readonly SwooleServerConfigDto $swoole_server_config
    ) {

    }


    public static function new(
        ?OpenIdConnectRestApiServerConfigDto $open_id_connect_rest_api_server_config = null
    ) : static {
        $open_id_connect_rest_api_server_config ??= OpenIdConnectRestApiServerConfigDto::newFromEnv();

        return new static(
            RestApi::new(),
            OpenIdConnectRestApiServerRouteCollector::new(
                OpenIdConnectApi::new(
                    $open_id_connect_rest_api_server_config->open_id_connect_api_config
                ),
                $open_id_connect_rest_api_server_config->cookie_config
            ),
            SwooleServerConfigDto::new(
                $open_id_connect_rest_api_server_config->https_cert,
                $open_id_connect_rest_api_server_config->https_key,
                $open_id_connect_rest_api_server_config->listen,
                $open_id_connect_rest_api_server_config->port
            )
        );
    }


    public function init() : void
    {
        $this->rest_api->initSwooleServer(
            $this->route_collector,
            null,
            $this->swoole_server_config
        );
    }
}
