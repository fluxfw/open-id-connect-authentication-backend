import { HttpServerResponse } from "../../../../../flux-http-api/src/Adapter/Server/HttpServerResponse.mjs";
import { OPEN_ID_CONNECT_DEFAULT_BASE_ROUTE } from "../../../../../flux-authentication-backend-api/src/Adapter/OpenIdConnect/OPEN_ID_CONNECT.mjs";
import { METHOD_GET, METHOD_HEAD, METHOD_OPTIONS } from "../../../../../flux-http-api/src/Adapter/Method/METHOD.mjs";

/** @typedef {import("../../../../../flux-authentication-backend-api/src/Adapter/Api/AuthenticationBackendApi.mjs").AuthenticationBackendApi} AuthenticationBackendApi */
/** @typedef {import("../../../../../flux-http-api/src/Adapter/Api/HttpApi.mjs").HttpApi} HttpApi */
/** @typedef {import("../../../../../flux-http-api/src/Adapter/Server/HttpServerRequest.mjs").HttpServerRequest} HttpServerRequest */

export class HandleRequestCommand {
    /**
     * @type {AuthenticationBackendApi}
     */
    #authentication_backend_api;
    /**
     * @type {string}
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
     * @returns {HandleRequestCommand}
     */
    static new(authentication_backend_api, http_api, base_route = null) {
        return new this(
            authentication_backend_api,
            http_api,
            base_route ?? OPEN_ID_CONNECT_DEFAULT_BASE_ROUTE
        );
    }

    /**
     * @param {AuthenticationBackendApi} authentication_backend_api
     * @param {HttpApi} http_api
     * @param {string} base_route
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
        if (request.url.pathname === `${this.#base_route}/callback` || request.url.pathname === `${this.#base_route}/login` || request.url.pathname === `${this.#base_route}/logout`) {
            return this.#authentication_backend_api.handleAuthentication(
                request
            );
        }

        if (request.url.pathname === `${this.#base_route}/user-infos`) {
            const response = await this.#http_api.validateMethods(
                request,
                [
                    METHOD_GET,
                    METHOD_HEAD,
                    METHOD_OPTIONS
                ]
            );

            if (response !== null) {
                return response;
            }

            const _response = await this.#authentication_backend_api.handleAuthentication(
                request
            );

            if (_response !== null) {
                return _response;
            }

            return HttpServerResponse.json(
                request._user_infos
            );
        }

        return null;
    }
}
