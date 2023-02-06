<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

use FluxOpenIdConnectRestApi\Adapter\Api\OpenIdConnectRestApi;
use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Body\Type\DefaultBodyType;
use FluxRestApi\Adapter\Cookie\CookieDto;
use FluxRestApi\Adapter\Header\DefaultHeaderKey;
use FluxRestApi\Adapter\Method\DefaultMethod;
use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteParamDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxRestApi\Adapter\Route\Route;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Status\DefaultStatus;

class CallbackRoute implements Route
{

    private function __construct(
        private readonly OpenIdConnectRestApi $open_id_connect_rest_api,
        private readonly CookieConfigDto $cookie_config
    ) {

    }


    public static function new(
        OpenIdConnectRestApi $open_id_connect_rest_api,
        CookieConfigDto $cookie_config
    ) : static {
        return new static(
            $open_id_connect_rest_api,
            $cookie_config
        );
    }


    public function getDocumentation() : ?RouteDocumentationDto
    {
        return RouteDocumentationDto::new(
            $this->getRoute(),
            $this->getMethod(),
            "Provider redirect callback",
            null,
            null,
            [
                RouteParamDocumentationDto::new(
                    "code",
                    "string"
                ),
                RouteParamDocumentationDto::new(
                    "error",
                    "string"
                ),
                RouteParamDocumentationDto::new(
                    "error_description",
                    "string"
                ),
                RouteParamDocumentationDto::new(
                    "state",
                    "string"
                )
            ],
            null,
            [
                RouteResponseDocumentationDto::new(
                    null,
                    DefaultStatus::_302,
                    null,
                    "Redirect to after login url"
                ),
                RouteResponseDocumentationDto::new(
                    DefaultBodyType::TEXT,
                    DefaultStatus::_403,
                    null,
                    "Invalid authorization"
                )
            ]
        );
    }


    public function getMethod() : Method
    {
        return DefaultMethod::GET;
    }


    public function getRoute() : string
    {
        return "/callback";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        [$encrypted_session, $redirect_url] = $this->open_id_connect_rest_api->callback(
            $request->getCookie(
                $this->cookie_config->name
            ),
            $request->query_params
        );

        if ($redirect_url !== null) {
            return ServerResponseDto::new(
                null,
                DefaultStatus::_302,
                [
                    DefaultHeaderKey::LOCATION->value => $redirect_url
                ],
                [
                    CookieDto::new(
                        $this->cookie_config->name,
                        $encrypted_session,
                        $this->cookie_config->expires_in,
                        $this->cookie_config->path,
                        $this->cookie_config->domain,
                        $this->cookie_config->secure,
                        $this->cookie_config->http_only,
                        $this->cookie_config->same_site,
                        $this->cookie_config->priority
                    )
                ]
            );
        } else {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Invalid authorization"
                ),
                DefaultStatus::_403,
                null,
                [
                    CookieDto::new(
                        $this->cookie_config->name,
                        null,
                        null,
                        $this->cookie_config->path,
                        $this->cookie_config->domain
                    )
                ]
            );
        }
    }
}
