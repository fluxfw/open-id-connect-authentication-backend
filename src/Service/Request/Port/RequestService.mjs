/** @typedef {import("../../../../../flux-authentication-backend-api/src/Adapter/Api/AuthenticationBackendApi.mjs").AuthenticationBackendApi} AuthenticationBackendApi */
/** @typedef {import("../../../../../flux-http-api/src/Adapter/Api/HttpApi.mjs").HttpApi} HttpApi */
/** @typedef {import("../../../../../flux-http-api/src/Adapter/Server/HttpServerRequest.mjs").HttpServerRequest} HttpServerRequest */
/** @typedef {import("../../../../../flux-http-api/src/Adapter/Server/HttpServerResponse.mjs").HttpServerResponse} HttpServerResponse */

export class RequestService {
    /**
     * @type {AuthenticationBackendApi}
     */
    #authentication_backend_api;
    /**
     * @type {string | null}
     */
    #base_route;
    /**
     * @type {HttpApi}
     */
    #http_api;

    /**
     * @param {AuthenticationBackendApi} authentication_backend_api
     * @param {HttpApi} http_api
     * @param {string | null} base_route
     * @returns {RequestService}
     */
    static new(authentication_backend_api, http_api, base_route = null) {
        return new this(
            authentication_backend_api,
            http_api,
            base_route
        );
    }

    /**
     * @param {AuthenticationBackendApi} authentication_backend_api
     * @param {HttpApi} http_api
     * @param {string | null} base_route
     * @private
     */
    constructor(authentication_backend_api, http_api, base_route) {
        this.#authentication_backend_api = authentication_backend_api;
        this.#http_api = http_api;
        this.#base_route = base_route;
    }

    /**
     * @param {HttpServerRequest} request
     * @returns {HttpServerResponse | null}
     */
    async handleRequest(request) {
        return (await import("../Command/HandleRequestCommand.mjs")).HandleRequestCommand.new(
            this.#authentication_backend_api,
            this.#http_api,
            this.#base_route
        )
            .handleRequest(
                request
            );
    }
}
