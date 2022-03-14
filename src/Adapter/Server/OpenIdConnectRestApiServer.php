<?php

namespace FluxOpenIdConnectRestApi\Adapter\Server;

use FluxOpenIdConnectRestApi\Libs\FluxOpenIdConnectApi\Adapter\Api\OpenIdConnectApi;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Collector\FolderRouteCollector;
use FluxOpenIdConnectRestApi\Libs\FluxRestApi\Adapter\Handler\SwooleHandler;
use Swoole\Http\Server;

class OpenIdConnectRestApiServer
{

    private function __construct(
        private readonly OpenIdConnectRestApiServerConfigDto $open_id_connect_rest_api_server_config,
        private readonly SwooleHandler $swoole_handler
    ) {

    }


    public static function new(
        ?OpenIdConnectRestApiServerConfigDto $open_id_connect_rest_api_server_config = null
    ) : static {
        $open_id_connect_rest_api_server_config ??= OpenIdConnectRestApiServerConfigDto::newFromEnv();

        return new static(
            $open_id_connect_rest_api_server_config,
            SwooleHandler::new(
                FolderRouteCollector::new(
                    __DIR__ . "/../Route",
                    [
                        OpenIdConnectApi::new(
                            $open_id_connect_rest_api_server_config->open_id_connect_api_config
                        ),
                        $open_id_connect_rest_api_server_config->cookie_config
                    ]
                )
            )
        );
    }


    public function init() : void
    {
        $options = [];
        $sock_type = SWOOLE_TCP;

        if ($this->open_id_connect_rest_api_server_config->https_cert !== null) {
            $options += [
                "ssl_cert_file" => $this->open_id_connect_rest_api_server_config->https_cert,
                "ssl_key_file"  => $this->open_id_connect_rest_api_server_config->https_key
            ];
            $sock_type += SWOOLE_SSL;
        }

        $server = new Server($this->open_id_connect_rest_api_server_config->listen, $this->open_id_connect_rest_api_server_config->port, SWOOLE_PROCESS, $sock_type);

        $server->set($options);

        $server->on("request", [$this->swoole_handler, "handle"]);

        $server->start();
    }
}
