#!/usr/bin/env php
<?php

require_once __DIR__ . "/../autoload.php";

use FluxOpenIdConnectRestApi\Adapter\Server\OpenIdConnectRestApiServer;

OpenIdConnectRestApiServer::new()
    ->init();
