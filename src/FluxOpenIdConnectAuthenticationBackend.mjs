import { CONFIG_ENV_PREFIX } from "./Config/CONFIG.mjs";
import { COOKIE_CONFIG_DOMAIN_KEY, COOKIE_CONFIG_HTTP_ONLY_KEY, COOKIE_CONFIG_NAME_KEY, COOKIE_CONFIG_PATH_KEY, COOKIE_CONFIG_PRIORITY_KEY, COOKIE_CONFIG_SAME_SITE_KEY, COOKIE_CONFIG_SECURE_KEY } from "./Cookie/COOKIE_CONFIG.mjs";
import { FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_COOKIE_NAME, FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_FRONTEND_BASE_ROUTE, FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_PROVIDER_SCOPE, FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_REDIRECT_AFTER_LOGIN_URL, FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_REDIRECT_AFTER_LOGOUT_URL } from "../../flux-authentication-backend/src/FluxOpenIdConnectAuthenticationBackend/FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND.mjs";
import { PROVIDER_CONFIG_CLIENT_ID_KEY, PROVIDER_CONFIG_CLIENT_SECRET_KEY, PROVIDER_CONFIG_HTTPS_CERTIFICATE_KEY, PROVIDER_CONFIG_REDIRECT_URI_KEY, PROVIDER_CONFIG_SCOPE_KEY, PROVIDER_CONFIG_URL_KEY } from "./Provider/PROVIDER_CONFIG.mjs";
import { ROUTE_CONFIG_FRONTEND_BASE_ROUTE_KEY, ROUTE_CONFIG_REDIRECT_AFTER_LOGIN_URL_KEY, ROUTE_CONFIG_REDIRECT_AFTER_LOGOUT_URL_KEY } from "./Route/ROUTE_CONFIG.mjs";
import { SERVER_CONFIG_HTTPS_CERTIFICATE_KEY, SERVER_CONFIG_HTTPS_DHPARAM_KEY, SERVER_CONFIG_HTTPS_KEY_KEY, SERVER_CONFIG_LISTEN_HTTP_PORT_KEY, SERVER_CONFIG_LISTEN_HTTPS_PORT_KEY, SERVER_CONFIG_LISTEN_INTERFACE_KEY, SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_KEY, SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_PORT_KEY, SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_STATUS_CODE_KEY } from "./Server/SERVER_CONFIG.mjs";
import { SERVER_DEFAULT_LISTEN_HTTP_PORT, SERVER_DEFAULT_LISTEN_HTTPS_PORT, SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS, SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS_PORT, SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS_STATUS_CODE } from "../../flux-http-api/src/Server/SERVER.mjs";
import { SET_COOKIE_OPTION_DEFAULT_HTTP_ONLY, SET_COOKIE_OPTION_DEFAULT_PATH, SET_COOKIE_OPTION_DEFAULT_PRIORITY, SET_COOKIE_OPTION_DEFAULT_SAME_SITE, SET_COOKIE_OPTION_DEFAULT_SECURE, SET_COOKIE_OPTION_DOMAIN, SET_COOKIE_OPTION_HTTP_ONLY, SET_COOKIE_OPTION_PATH, SET_COOKIE_OPTION_PRIORITY, SET_COOKIE_OPTION_SAME_SITE, SET_COOKIE_OPTION_SECURE } from "../../flux-http-api/src/Cookie/SET_COOKIE_OPTION.mjs";

/** @typedef {import("../../flux-authentication-backend/src/FluxAuthenticationBackend.mjs").FluxAuthenticationBackend} FluxAuthenticationBackend */
/** @typedef {import("../../flux-config-api/src/FluxConfigApi.mjs").FluxConfigApi} FluxConfigApi */
/** @typedef {import("../../flux-http-api/src/FluxHttpApi.mjs").FluxHttpApi} FluxHttpApi */
/** @typedef {import("../../flux-shutdown-handler/src/FluxShutdownHandler.mjs").FluxShutdownHandler} FluxShutdownHandler */
/** @typedef {import("./Request/Port/RequestService.mjs").RequestService} RequestService */

export class FluxOpenIdConnectAuthenticationBackend {
    /**
     * @type {FluxAuthenticationBackend | null}
     */
    #flux_authentication_backend = null;
    /**
     * @type {FluxConfigApi | null}
     */
    #flux_config_api = null;
    /**
     * @type {FluxHttpApi | null}
     */
    #flux_http_api = null;
    /**
     * @type {FluxShutdownHandler}
     */
    #flux_shutdown_handler;
    /**
     * @type {RequestService | null}
     */
    #request_service = null;

    /**
     * @param {FluxShutdownHandler} flux_shutdown_handler
     * @returns {FluxOpenIdConnectAuthenticationBackend}
     */
    static new(flux_shutdown_handler) {
        return new this(
            flux_shutdown_handler
        );
    }

    /**
     * @param {FluxShutdownHandler} flux_shutdown_handler
     * @private
     */
    constructor(flux_shutdown_handler) {
        this.#flux_shutdown_handler = flux_shutdown_handler;
    }

    /**
     * @returns {Promise<void>}
     */
    async runServer() {
        const flux_config_api = await this.#getFluxConfigApi();

        await (await this.#getFluxHttpApi()).runServer(
            async request => (await this.#getRequestService()).handleRequest(
                request
            ),
            {
                forwarded_headers: true,
                https_certificate: await flux_config_api.getConfig(
                    SERVER_CONFIG_HTTPS_CERTIFICATE_KEY
                ),
                https_dhparam: await flux_config_api.getConfig(
                    SERVER_CONFIG_HTTPS_DHPARAM_KEY
                ),
                https_key: await flux_config_api.getConfig(
                    SERVER_CONFIG_HTTPS_KEY_KEY
                ),
                listen_http_port: await flux_config_api.getConfig(
                    SERVER_CONFIG_LISTEN_HTTP_PORT_KEY,
                    SERVER_DEFAULT_LISTEN_HTTP_PORT
                ),
                listen_https_port: await flux_config_api.getConfig(
                    SERVER_CONFIG_LISTEN_HTTPS_PORT_KEY,
                    SERVER_DEFAULT_LISTEN_HTTPS_PORT
                ),
                listen_interface: await flux_config_api.getConfig(
                    SERVER_CONFIG_LISTEN_INTERFACE_KEY
                ),
                redirect_http_to_https: await flux_config_api.getConfig(
                    SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_KEY,
                    SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS
                ),
                redirect_http_to_https_port: await flux_config_api.getConfig(
                    SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_PORT_KEY,
                    SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS_PORT
                ),
                redirect_http_to_https_status_code: await flux_config_api.getConfig(
                    SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_STATUS_CODE_KEY,
                    SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS_STATUS_CODE
                )
            }
        );
    }

    /**
     * @returns {Promise<FluxAuthenticationBackend>}
     */
    async #getFluxAuthenticationBackend() {
        if (this.#flux_authentication_backend === null) {
            const flux_config_api = await this.#getFluxConfigApi();

            this.#flux_authentication_backend ??= (await import("../../flux-authentication-backend/src/FluxOpenIdConnectAuthenticationBackend/FluxOpenIdConnectAuthenticationBackend.mjs")).FluxOpenIdConnectAuthenticationBackend.new(
                await this.#getFluxHttpApi(),
                await flux_config_api.getConfig(
                    PROVIDER_CONFIG_URL_KEY
                ),
                await flux_config_api.getConfig(
                    PROVIDER_CONFIG_CLIENT_ID_KEY
                ),
                await flux_config_api.getConfig(
                    PROVIDER_CONFIG_CLIENT_SECRET_KEY
                ),
                await flux_config_api.getConfig(
                    PROVIDER_CONFIG_REDIRECT_URI_KEY
                ),
                await flux_config_api.getConfig(
                    PROVIDER_CONFIG_SCOPE_KEY,
                    FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_PROVIDER_SCOPE
                ),
                await flux_config_api.getConfig(
                    PROVIDER_CONFIG_HTTPS_CERTIFICATE_KEY
                ),
                await flux_config_api.getConfig(
                    COOKIE_CONFIG_NAME_KEY,
                    FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_COOKIE_NAME
                ),
                {
                    [SET_COOKIE_OPTION_DOMAIN]: await flux_config_api.getConfig(
                        COOKIE_CONFIG_DOMAIN_KEY
                    ),
                    [SET_COOKIE_OPTION_HTTP_ONLY]: await flux_config_api.getConfig(
                        COOKIE_CONFIG_HTTP_ONLY_KEY,
                        SET_COOKIE_OPTION_DEFAULT_HTTP_ONLY
                    ),
                    [SET_COOKIE_OPTION_PATH]: await flux_config_api.getConfig(
                        COOKIE_CONFIG_PATH_KEY,
                        SET_COOKIE_OPTION_DEFAULT_PATH
                    ),
                    [SET_COOKIE_OPTION_PRIORITY]: await flux_config_api.getConfig(
                        COOKIE_CONFIG_PRIORITY_KEY,
                        SET_COOKIE_OPTION_DEFAULT_PRIORITY
                    ),
                    [SET_COOKIE_OPTION_SAME_SITE]: await flux_config_api.getConfig(
                        COOKIE_CONFIG_SAME_SITE_KEY,
                        SET_COOKIE_OPTION_DEFAULT_SAME_SITE
                    ),
                    [SET_COOKIE_OPTION_SECURE]: await flux_config_api.getConfig(
                        COOKIE_CONFIG_SECURE_KEY,
                        SET_COOKIE_OPTION_DEFAULT_SECURE
                    )
                },
                "/api",
                await flux_config_api.getConfig(
                    ROUTE_CONFIG_FRONTEND_BASE_ROUTE_KEY,
                    FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_FRONTEND_BASE_ROUTE
                ),
                await flux_config_api.getConfig(
                    ROUTE_CONFIG_REDIRECT_AFTER_LOGIN_URL_KEY,
                    FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_REDIRECT_AFTER_LOGIN_URL
                ),
                await flux_config_api.getConfig(
                    ROUTE_CONFIG_REDIRECT_AFTER_LOGOUT_URL_KEY,
                    FLUX_OPEN_ID_CONNECT_AUTHENTICATION_BACKEND_DEFAULT_REDIRECT_AFTER_LOGOUT_URL
                )
            );
        }

        return this.#flux_authentication_backend;
    }

    /**
     * @returns {Promise<FluxConfigApi>}
     */
    async #getFluxConfigApi() {
        this.#flux_config_api ??= (await import("../../flux-config-api/src/FluxConfigApi.mjs")).FluxConfigApi.new(
            await (await import("../../flux-config-api/src/getValueProviderImplementations.mjs")).getValueProviderImplementations(
                CONFIG_ENV_PREFIX
            )
        );

        return this.#flux_config_api;
    }

    /**
     * @returns {Promise<FluxHttpApi>}
     */
    async #getFluxHttpApi() {
        this.#flux_http_api ??= (await import("../../flux-http-api/src/FluxHttpApi.mjs")).FluxHttpApi.new(
            this.#flux_shutdown_handler
        );

        return this.#flux_http_api;
    }

    /**
     * @returns {Promise<RequestService>}
     */
    async #getRequestService() {
        this.#request_service ??= (await import("./Request/Port/RequestService.mjs")).RequestService.new(
            await this.#getFluxAuthenticationBackend(),
            await this.#getFluxHttpApi()
        );

        return this.#request_service;
    }
}
