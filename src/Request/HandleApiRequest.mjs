import { HttpServerResponse } from "../../../flux-http-api/src/Server/HttpServerResponse.mjs";
import { METHOD_GET, METHOD_HEAD } from "../../../flux-http-api/src/Method/METHOD.mjs";

/** @typedef {import("../../../flux-http-api/src/FluxHttpApi.mjs").FluxHttpApi} FluxHttpApi */
/** @typedef {import("../../../flux-http-api/src/Server/HttpServerRequest.mjs").HttpServerRequest} HttpServerRequest */
/** @typedef {import("../../../flux-authentication-backend/src/UserInfo.mjs").UserInfo} UserInfo */

export class HandleApiRequest {
    /**
     * @type {FluxHttpApi}
     */
    #flux_http_api;

    /**
     * @param {FluxHttpApi} flux_http_api
     * @returns {HandleApiRequest}
     */
    static new(flux_http_api) {
        return new this(
            flux_http_api
        );
    }

    /**
     * @param {FluxHttpApi} flux_http_api
     * @private
     */
    constructor(flux_http_api) {
        this.#flux_http_api = flux_http_api;
    }

    /**
     * @param {HttpServerRequest} request
     * @param {UserInfo} user_infos
     * @returns {Promise<HttpServerResponse | null>}
     */
    async handleApiRequest(request, user_infos) {
        if (request.url.pathname === "/api/user-infos") {
            const response = await this.#flux_http_api.validateMethods(
                request,
                [
                    METHOD_GET,
                    METHOD_HEAD
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
