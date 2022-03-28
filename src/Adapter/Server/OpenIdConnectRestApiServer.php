<?php

namespace FluxOpenIdConnectRestApi\Adapter\Server;

use FluxOpenIdConnectRestApi\Libs\FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Server\SwooleRestApiServer;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Server\SwooleRestApiServerConfigDto;

class OpenIdConnectRestApiServer
{

    private function __construct(
        private readonly SwooleRestApiServer $swoole_rest_api_server
    ) {

    }


    public static function new(
        ?OpenIdConnectRestApiServerConfigDto $open_id_connect_rest_api_server_config = null
    ) : static {
        $open_id_connect_rest_api_server_config ??= OpenIdConnectRestApiServerConfigDto::newFromEnv();

        return new static(
            SwooleRestApiServer::new(
                OpenIdConnectRestApiServerRouteCollector::new(
                    OpenIdConnectApi::new(
                        $open_id_connect_rest_api_server_config->open_id_connect_api_config
                    ),
                    $open_id_connect_rest_api_server_config->cookie_config
                ),
                null,
                SwooleRestApiServerConfigDto::new(
                    $open_id_connect_rest_api_server_config->https_cert,
                    $open_id_connect_rest_api_server_config->https_key,
                    $open_id_connect_rest_api_server_config->listen,
                    $open_id_connect_rest_api_server_config->port
                )
            )
        );
    }


    public function init() : void
    {
        $this->swoole_rest_api_server->init();
    }
}
