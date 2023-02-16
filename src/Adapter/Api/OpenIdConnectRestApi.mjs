import { CONFIG_ENV_PREFIX } from "../Config/CONFIG.mjs";
import { OPEN_ID_CONNECT_CONFIG_BASE_ROUTE_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_DOMAIN_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_HTTP_ONLY_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_KEY_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_MAX_AGE_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_NAME_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_PATH_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_PRIORITY_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_SAME_SITE_KEY, OPEN_ID_CONNECT_CONFIG_COOKIE_SECURE_KEY, OPEN_ID_CONNECT_CONFIG_PROVIDER_CERTIFICATE_KEY, OPEN_ID_CONNECT_CONFIG_PROVIDER_CLIENT_ID_KEY, OPEN_ID_CONNECT_CONFIG_PROVIDER_CLIENT_SECRET_KEY, OPEN_ID_CONNECT_CONFIG_PROVIDER_REDIRECT_URI_KEY, OPEN_ID_CONNECT_CONFIG_PROVIDER_SCOPE_KEY, OPEN_ID_CONNECT_CONFIG_PROVIDER_URL_KEY, OPEN_ID_CONNECT_CONFIG_REDIRECT_LOGIN_URL, OPEN_ID_CONNECT_CONFIG_REDIRECT_LOGOUT_URL } from "../OpenIdConnect/OPEN_ID_CONNECT_CONFIG.mjs";
import { OPEN_ID_CONNECT_DEFAULT_BASE_ROUTE, OPEN_ID_CONNECT_DEFAULT_COOKIE_NAME, OPEN_ID_CONNECT_DEFAULT_PROVIDER_SCOPE, OPEN_ID_CONNECT_DEFAULT_REDIRECT_LOGIN_URL, OPEN_ID_CONNECT_DEFAULT_REDIRECT_LOGOUT_URL } from "../../../../flux-authentication-backend-api/src/Adapter/OpenIdConnect/OPEN_ID_CONNECT.mjs";
import { SERVER_CONFIG_HTTPS_CERTIFICATE_KEY, SERVER_CONFIG_HTTPS_DHPARAM_KEY, SERVER_CONFIG_HTTPS_KEY_KEY, SERVER_CONFIG_LISTEN_HTTP_PORT_KEY, SERVER_CONFIG_LISTEN_HTTPS_PORT_KEY, SERVER_CONFIG_LISTEN_INTERFACE_KEY, SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_KEY, SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_PORT_KEY, SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_STATUS_CODE_KEY } from "../Server/SERVER_CONFIG.mjs";
import { SERVER_DEFAULT_LISTEN_HTTP_PORT, SERVER_DEFAULT_LISTEN_HTTPS_PORT, SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS, SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS_PORT, SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS_STATUS_CODE } from "../../../../flux-http-api/src/Adapter/Server/SERVER.mjs";
import { SET_COOKIE_OPTION_DEFAULT_HTTP_ONLY, SET_COOKIE_OPTION_DEFAULT_MAX_AGE, SET_COOKIE_OPTION_DEFAULT_PATH, SET_COOKIE_OPTION_DEFAULT_PRIORITY, SET_COOKIE_OPTION_DEFAULT_SAME_SITE, SET_COOKIE_OPTION_DEFAULT_SECURE, SET_COOKIE_OPTION_DOMAIN, SET_COOKIE_OPTION_HTTP_ONLY, SET_COOKIE_OPTION_MAX_AGE, SET_COOKIE_OPTION_PATH, SET_COOKIE_OPTION_PRIORITY, SET_COOKIE_OPTION_SAME_SITE, SET_COOKIE_OPTION_SECURE } from "../../../../flux-http-api/src/Adapter/Cookie/SET_COOKIE_OPTION.mjs";

/** @typedef {import("../../../../flux-authentication-backend-api/src/Adapter/Api/AuthenticationBackendApi.mjs").AuthenticationBackendApi} AuthenticationBackendApi */
/** @typedef {import("../../../../flux-config-api/src/Adapter/Api/ConfigApi.mjs").ConfigApi} ConfigApi */
/** @typedef {import("../../../../flux-http-api/src/Adapter/Api/HttpApi.mjs").HttpApi} HttpApi */
/** @typedef {import("../../Service/Request/Port/RequestService.mjs").RequestService} RequestService */
/** @typedef {import("../../../../flux-shutdown-handler-api/src/Adapter/ShutdownHandler/ShutdownHandler.mjs").ShutdownHandler} ShutdownHandler */

export class OpenIdConnectRestApi {
    /**
     * @type {AuthenticationBackendApi | null}
     */
    #authentication_backend_api = null;
    /**
     * @type {ConfigApi | null}
     */
    #config_api = null;
    /**
     * @type {HttpApi | null}
     */
    #http_api = null;
    /**
     * @type {RequestService | null}
     */
    #request_service = null;
    /**
     * @type {ShutdownHandler}
     */
    #shutdown_handler;

    /**
     * @param {ShutdownHandler} shutdown_handler
     * @returns {OpenIdConnectRestApi}
     */
    static new(shutdown_handler) {
        return new this(
            shutdown_handler
        );
    }

    /**
     * @param {ShutdownHandler} shutdown_handler
     * @private
     */
    constructor(shutdown_handler) {
        this.#shutdown_handler = shutdown_handler;
    }

    /**
     * @returns {Promise<void>}
     */
    async runServer() {
        const config_api = await this.#getConfigApi();

        await (await this.#getHttpApi()).runServer(
            async request => (await this.#getRequestService()).handleRequest(
                request
            ),
            {
                https_certificate: await config_api.getConfig(
                    SERVER_CONFIG_HTTPS_CERTIFICATE_KEY
                ),
                https_dhparam: await config_api.getConfig(
                    SERVER_CONFIG_HTTPS_DHPARAM_KEY
                ),
                https_key: await config_api.getConfig(
                    SERVER_CONFIG_HTTPS_KEY_KEY
                ),
                listen_http_port: await config_api.getConfig(
                    SERVER_CONFIG_LISTEN_HTTP_PORT_KEY,
                    SERVER_DEFAULT_LISTEN_HTTP_PORT
                ),
                listen_https_port: await config_api.getConfig(
                    SERVER_CONFIG_LISTEN_HTTPS_PORT_KEY,
                    SERVER_DEFAULT_LISTEN_HTTPS_PORT
                ),
                listen_interface: await config_api.getConfig(
                    SERVER_CONFIG_LISTEN_INTERFACE_KEY
                ),
                redirect_http_to_https: await config_api.getConfig(
                    SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_KEY,
                    SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS
                ),
                redirect_http_to_https_port: await config_api.getConfig(
                    SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_PORT_KEY,
                    SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS_PORT
                ),
                redirect_http_to_https_status_code: await config_api.getConfig(
                    SERVER_CONFIG_REDIRECT_HTTP_TO_HTTPS_STATUS_CODE_KEY,
                    SERVER_DEFAULT_REDIRECT_HTTP_TO_HTTPS_STATUS_CODE
                )
            }
        );
    }

    /**
     * @returns {Promise<AuthenticationBackendApi>}
     */
    async #getAuthenticationBackendApi() {
        const config_api = await this.#getConfigApi();

        this.#authentication_backend_api ??= (await import("../../../../flux-authentication-backend-api/src/Adapter/Api/AuthenticationBackendApi.mjs")).AuthenticationBackendApi.new(
            (await import("../../../../flux-authentication-backend-api/src/Adapter/AuthenticationImplementation/OpenIdConnectAuthenticationImplementation.mjs")).OpenIdConnectAuthenticationImplementation.new(
                await this.#getHttpApi(),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_PROVIDER_URL_KEY
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_PROVIDER_CLIENT_ID_KEY
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_PROVIDER_CLIENT_SECRET_KEY
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_PROVIDER_REDIRECT_URI_KEY
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_PROVIDER_SCOPE_KEY,
                    OPEN_ID_CONNECT_DEFAULT_PROVIDER_SCOPE
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_PROVIDER_CERTIFICATE_KEY
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_COOKIE_NAME_KEY,
                    OPEN_ID_CONNECT_DEFAULT_COOKIE_NAME
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_COOKIE_KEY_KEY
                ),
                {
                    [SET_COOKIE_OPTION_DOMAIN]: await config_api.getConfig(
                        OPEN_ID_CONNECT_CONFIG_COOKIE_DOMAIN_KEY
                    ),
                    [SET_COOKIE_OPTION_HTTP_ONLY]: await config_api.getConfig(
                        OPEN_ID_CONNECT_CONFIG_COOKIE_HTTP_ONLY_KEY,
                        SET_COOKIE_OPTION_DEFAULT_HTTP_ONLY
                    ),
                    [SET_COOKIE_OPTION_MAX_AGE]: await config_api.getConfig(
                        OPEN_ID_CONNECT_CONFIG_COOKIE_MAX_AGE_KEY,
                        SET_COOKIE_OPTION_DEFAULT_MAX_AGE
                    ),
                    [SET_COOKIE_OPTION_PATH]: await config_api.getConfig(
                        OPEN_ID_CONNECT_CONFIG_COOKIE_PATH_KEY,
                        SET_COOKIE_OPTION_DEFAULT_PATH
                    ),
                    [SET_COOKIE_OPTION_PRIORITY]: await config_api.getConfig(
                        OPEN_ID_CONNECT_CONFIG_COOKIE_PRIORITY_KEY,
                        SET_COOKIE_OPTION_DEFAULT_PRIORITY
                    ),
                    [SET_COOKIE_OPTION_SAME_SITE]: await config_api.getConfig(
                        OPEN_ID_CONNECT_CONFIG_COOKIE_SAME_SITE_KEY,
                        SET_COOKIE_OPTION_DEFAULT_SAME_SITE
                    ),
                    [SET_COOKIE_OPTION_SECURE]: await config_api.getConfig(
                        OPEN_ID_CONNECT_CONFIG_COOKIE_SECURE_KEY,
                        SET_COOKIE_OPTION_DEFAULT_SECURE
                    )
                },
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_BASE_ROUTE_KEY,
                    OPEN_ID_CONNECT_DEFAULT_BASE_ROUTE
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_REDIRECT_LOGIN_URL,
                    OPEN_ID_CONNECT_DEFAULT_REDIRECT_LOGIN_URL
                ),
                await config_api.getConfig(
                    OPEN_ID_CONNECT_CONFIG_REDIRECT_LOGOUT_URL,
                    OPEN_ID_CONNECT_DEFAULT_REDIRECT_LOGOUT_URL
                )
            )
        );

        return this.#authentication_backend_api;
    }

    /**
     * @returns {Promise<ConfigApi>}
     */
    async #getConfigApi() {
        this.#config_api ??= (await import("../../../../flux-config-api/src/Adapter/Api/ConfigApi.mjs")).ConfigApi.new(
            await (await import("../../../../flux-config-api/src/Adapter/ValueProviderImplementation/getValueProviderImplementations.mjs")).getValueProviderImplementations(
                CONFIG_ENV_PREFIX
            )
        );

        return this.#config_api;
    }

    /**
     * @returns {Promise<HttpApi>}
     */
    async #getHttpApi() {
        this.#http_api ??= (await import("../../../../flux-http-api/src/Adapter/Api/HttpApi.mjs")).HttpApi.new(
            this.#shutdown_handler
        );

        return this.#http_api;
    }

    /**
     * @returns {Promise<RequestService>}
     */
    async #getRequestService() {
        this.#request_service ??= (await import("../../Service/Request/Port/RequestService.mjs")).RequestService.new(
            await this.#getAuthenticationBackendApi(),
            await this.#getHttpApi(),
            await (await this.#getConfigApi()).getConfig(
                OPEN_ID_CONNECT_CONFIG_BASE_ROUTE_KEY,
                OPEN_ID_CONNECT_DEFAULT_BASE_ROUTE
            )
        );

        return this.#request_service;
    }
}
