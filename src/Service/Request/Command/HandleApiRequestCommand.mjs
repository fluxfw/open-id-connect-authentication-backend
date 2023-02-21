import { HttpServerResponse } from "../../../../../flux-http-api/src/Adapter/Server/HttpServerResponse.mjs";
import { METHOD_GET, METHOD_HEAD, METHOD_OPTIONS } from "../../../../../flux-http-api/src/Adapter/Method/METHOD.mjs";

/** @typedef {import("../../../../../flux-authentication-backend-api/src/Adapter/Api/AuthenticationBackendApi.mjs").AuthenticationBackendApi} AuthenticationBackendApi */
/** @typedef {import("../../../../../flux-http-api/src/Adapter/Api/HttpApi.mjs").HttpApi} HttpApi */
/** @typedef {import("../../../../../flux-http-api/src/Adapter/Server/HttpServerRequest.mjs").HttpServerRequest} HttpServerRequest */

export class HandleApiRequestCommand {
    /**
     * @type {AuthenticationBackendApi}
     */
    #authentication_backend_api;
    /**
     * @type {HttpApi}
     */
    #http_api;

    /**
     * @param {AuthenticationBackendApi} authentication_backend_api
     * @param {HttpApi} http_api
     * @returns {HandleApiRequestCommand}
     */
    static new(authentication_backend_api, http_api) {
        return new this(
            authentication_backend_api,
            http_api
        );
    }

    /**
     * @param {AuthenticationBackendApi} authentication_backend_api
     * @param {HttpApi} http_api
     * @private
     */
    constructor(authentication_backend_api, http_api) {
        this.#authentication_backend_api = authentication_backend_api;
        this.#http_api = http_api;
    }

    /**
     * @param {HttpServerRequest} request
     * @returns {HttpServerResponse | null}
     */
    async handleApiRequest(request) {
        const user_infos = await this.#authentication_backend_api.handleAuthentication(
            request
        );

        if (user_infos instanceof HttpServerResponse) {
            return user_infos;
        }

        if (request.url.pathname === "/api/user-infos") {
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

            return HttpServerResponse.json(
                user_infos
            );
        }

        return null;
    }
}
