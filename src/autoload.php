<?php

namespace FluxOpenIdConnectRestApi;

require_once __DIR__ . "/../libs/flux-autoload-api/autoload.php";
require_once __DIR__ . "/../libs/flux-open-id-connect-api/autoload.php";
require_once __DIR__ . "/../libs/flux-rest-api/autoload.php";

use FluxOpenIdConnectRestApi\Libs\FluxAutoloadApi\Adapter\Autoload\Psr4Autoload;
use FluxOpenIdConnectRestApi\Libs\FluxAutoloadApi\Adapter\Checker\PhpExtChecker;
use FluxOpenIdConnectRestApi\Libs\FluxAutoloadApi\Adapter\Checker\PhpVersionChecker;

PhpVersionChecker::new(
    ">=8.2"
)
    ->checkAndDie(
        __NAMESPACE__
    );
PhpExtChecker::new(
    [
        "swoole"
    ]
)
    ->checkAndDie(
        __NAMESPACE__
    );

Psr4Autoload::new(
    [
        __NAMESPACE__ => __DIR__
    ]
)
    ->autoload();
