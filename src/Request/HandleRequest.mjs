/** @typedef {import("../../../flux-authentication-backend/src/FluxAuthenticationBackend.mjs").FluxAuthenticationBackend} FluxAuthenticationBackend */
/** @typedef {import("../../../flux-http-api/src/FluxHttpApi.mjs").FluxHttpApi} FluxHttpApi */
/** @typedef {import("../../../flux-http-api/src/Server/HttpServerRequest.mjs").HttpServerRequest} HttpServerRequest */
/** @typedef {import("../../../flux-http-api/src/Server/HttpServerResponse.mjs").HttpServerResponse} HttpServerResponse */

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
     * @param {FluxAuthenticationBackend} flux_authentication_backend
     * @param {FluxHttpApi} flux_http_api
     * @returns {HandleRequest}
     */
    static new(flux_authentication_backend, flux_http_api) {
        return new this(
            flux_authentication_backend,
            flux_http_api
        );
    }

    /**
     * @param {FluxAuthenticationBackend} flux_authentication_backend
     * @param {FluxHttpApi} flux_http_api
     * @private
     */
    constructor(flux_authentication_backend, flux_http_api) {
        this.#flux_authentication_backend = flux_authentication_backend;
        this.#flux_http_api = flux_http_api;
    }

    /**
     * @param {HttpServerRequest} request
     * @returns {HttpServerResponse | null}
     */
    async handleRequest(request) {
        if (request.url.pathname.startsWith("/api/") || request.url.pathname === "/api") {
            return (await import("./HandleApiRequest.mjs")).HandleApiRequest.new(
                this.#flux_authentication_backend,
                this.#flux_http_api
            )
                .handleApiRequest(
                    request
                );
        }

        return null;
    }
}
