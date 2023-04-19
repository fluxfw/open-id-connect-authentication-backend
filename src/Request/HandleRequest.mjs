import { HttpServerResponse } from "../../../flux-http-api/src/Server/HttpServerResponse.mjs";

/** @typedef {import("../../../flux-authentication-backend/src/FluxAuthenticationBackend.mjs").FluxAuthenticationBackend} FluxAuthenticationBackend */
/** @typedef {import("../../../flux-http-api/src/FluxHttpApi.mjs").FluxHttpApi} FluxHttpApi */
/** @typedef {import("../../../flux-http-api/src/Server/HttpServerRequest.mjs").HttpServerRequest} HttpServerRequest */

export class HandleRequest {
    /**
     * @type {FluxAuthenticationBackend}
     */
    #flux_authentication_backend;
    /**
     * @type {FluxHttpApi}
     */
    #flux_http_api;
    /**
     * @type {FluxAuthenticationBackend}
     */
    #flux_open_id_connect_authentication_backend;

    /**
     * @param {FluxAuthenticationBackend} flux_authentication_backend
     * @param {FluxHttpApi} flux_http_api
     * @param {FluxAuthenticationBackend} flux_open_id_connect_authentication_backend
     * @returns {HandleRequest}
     */
    static new(flux_authentication_backend, flux_http_api, flux_open_id_connect_authentication_backend) {
        return new this(
            flux_authentication_backend,
            flux_http_api,
            flux_open_id_connect_authentication_backend
        );
    }

    /**
     * @param {FluxAuthenticationBackend} flux_authentication_backend
     * @param {FluxHttpApi} flux_http_api
     * @param {FluxAuthenticationBackend} flux_open_id_connect_authentication_backend
     * @private
     */
    constructor(flux_authentication_backend, flux_http_api, flux_open_id_connect_authentication_backend) {
        this.#flux_authentication_backend = flux_authentication_backend;
        this.#flux_http_api = flux_http_api;
        this.#flux_open_id_connect_authentication_backend = flux_open_id_connect_authentication_backend;
    }

    /**
     * @param {HttpServerRequest} request
     * @returns {Promise<HttpServerResponse | null>}
     */
    async handleRequest(request) {
        const user_infos = await this.#flux_authentication_backend.handleAuthentication(
            request
        );

        if (user_infos instanceof HttpServerResponse) {
            return user_infos;
        }

        const _user_infos = await this.#flux_open_id_connect_authentication_backend.handleAuthentication(
            request
        );

        if (_user_infos instanceof HttpServerResponse) {
            return _user_infos;
        }

        if (request.url.pathname.startsWith("/api/") || request.url.pathname === "/api") {
            return (await import("./HandleApiRequest.mjs")).HandleApiRequest.new(
                this.#flux_http_api
            )
                .handleApiRequest(
                    request,
                    _user_infos
                );
        }

        return null;
    }
}
