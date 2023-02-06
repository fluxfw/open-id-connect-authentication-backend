<?php

namespace FluxOpenIdConnectRestApi\Adapter\Route;

use FluxOpenIdConnectRestApi\Adapter\Api\OpenIdConnectRestApi;
use FluxOpenIdConnectRestApi\Adapter\Cookie\CookieConfigDto;
use FluxOpenIdConnectRestApi\Adapter\UserInfo\UserInfosDto;
use FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxRestApi\Adapter\Body\TextBodyDto;
use FluxRestApi\Adapter\Body\Type\DefaultBodyType;
use FluxRestApi\Adapter\Cookie\CookieDto;
use FluxRestApi\Adapter\Method\DefaultMethod;
use FluxRestApi\Adapter\Method\Method;
use FluxRestApi\Adapter\Route\Documentation\RouteDocumentationDto;
use FluxRestApi\Adapter\Route\Documentation\RouteResponseDocumentationDto;
use FluxRestApi\Adapter\Route\Route;
use FluxRestApi\Adapter\Server\ServerRequestDto;
use FluxRestApi\Adapter\Server\ServerResponseDto;
use FluxRestApi\Adapter\Status\DefaultStatus;

class UserInfosRoute implements Route
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
            "Get user infos",
            null,
            null,
            null,
            null,
            [
                RouteResponseDocumentationDto::new(
                    DefaultBodyType::JSON,
                    null,
                    UserInfosDto::class,
                    "User infos"
                ),
                RouteResponseDocumentationDto::new(
                    DefaultBodyType::TEXT,
                    DefaultStatus::_401,
                    null,
                    "Authorization needed"
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
        return "/userinfos";
    }


    public function handle(ServerRequestDto $request) : ?ServerResponseDto
    {
        [$user_infos, $encrypted_session] = $this->open_id_connect_rest_api->getUserInfos(
            $request->getCookie(
                $this->cookie_config->name
            )
        );

        if ($user_infos !== null) {
            return ServerResponseDto::new(
                JsonBodyDto::new(
                    $user_infos
                ),
                null,
                null,
                $encrypted_session !== null ? [
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
                ] : null
            );
        } else {
            return ServerResponseDto::new(
                TextBodyDto::new(
                    "Authorization needed"
                ),
                DefaultStatus::_401,
                null,
                $encrypted_session !== null ? [
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
                ] : null
            );
        }
    }
}
