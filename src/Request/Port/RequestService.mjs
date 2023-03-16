/** @typedef {import("../../../../flux-authentication-backend/src/FluxAuthenticationBackend.mjs").FluxAuthenticationBackend} FluxAuthenticationBackend */
/** @typedef {import("../../../../flux-http-api/src/FluxHttpApi.mjs").FluxHttpApi} FluxHttpApi */
/** @typedef {import("../../../../flux-http-api/src/Server/HttpServerRequest.mjs").HttpServerRequest} HttpServerRequest */
/** @typedef {import("../../../../flux-http-api/src/Server/HttpServerResponse.mjs").HttpServerResponse} HttpServerResponse */

export class RequestService {
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
     * @returns {RequestService}
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
    async handleApiRequest(request) {
        return (await import("../Command/HandleApiRequestCommand.mjs")).HandleApiRequestCommand.new(
            this.#flux_authentication_backend,
            this.#flux_http_api
        )
            .handleApiRequest(
                request
            );
    }

    /**
     * @param {HttpServerRequest} request
     * @returns {HttpServerResponse | null}
     */
    async handleRequest(request) {
        return (await import("../Command/HandleRequestCommand.mjs")).HandleRequestCommand.new(
            this
        )
            .handleRequest(
                request
            );
    }
}
