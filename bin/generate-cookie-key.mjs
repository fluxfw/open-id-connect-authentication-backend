#!/usr/bin/env node
let shutdown_handler = null;
try {
    shutdown_handler = await (await import("../../flux-shutdown-handler-api/src/Adapter/Api/ShutdownHandlerApi.mjs")).ShutdownHandlerApi.new()
        .getShutdownHandler();

    process.stdout.write(`${await (await import("../src/Adapter/Api/OpenIdConnectAuthenticationBackendApi.mjs")).OpenIdConnectAuthenticationBackendApi.new(
        shutdown_handler
    )
        .generateCookieKey()}
`);
} catch (error) {
    console.error(error);

    if (shutdown_handler !== null) {
        await shutdown_handler.shutdown(
            1
        );
    } else {
        process.exit(1);
    }
}
