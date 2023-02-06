<?php

namespace FluxOpenIdConnectRestApi\Adapter\Api;

use FluxOpenIdConnectRestApi\Service\OpenIdConnect\Port\OpenIdConnectService;
use FluxOpenIdConnectRestApi\Service\Request\Port\RequestService;
use FluxRestApi\Adapter\Api\RestApi;

class OpenIdConnectRestApi
{

    private function __construct(
        private readonly OpenIdConnectRestApiConfigDto $open_id_connect_rest_api_config
    ) {

    }


    public static function new(
        ?OpenIdConnectRestApiConfigDto $open_id_connect_rest_api_config = null
    ) : static {
        return new static(
            $open_id_connect_rest_api_config ?? OpenIdConnectRestApiConfigDto::newFromEnv()
        );
    }


    /**
     * @param string[] $query_params
     *
     * @return string[]
     */
    public function callback(?string $encrypted_session, array $query_params) : array
    {
        return $this->getOpenIdConnectService()
            ->callback(
                $encrypted_session,
                $query_params
            );
    }


    public function getUserInfos(?string $encrypted_session) : array
    {
        return $this->getOpenIdConnectService()
            ->getUserInfos(
                $encrypted_session
            );
    }


    /**
     * @return string[]
     */
    public function login() : array
    {
        return $this->getOpenIdConnectService()
            ->login();
    }


    public function logout() : string
    {
        return $this->getOpenIdConnectService()
            ->logout();
    }


    private function getOpenIdConnectService() : OpenIdConnectService
    {
        return OpenIdConnectService::new(
            $this->open_id_connect_rest_api_config->open_id_config,
            $this->open_id_connect_rest_api_config->route_config,
            $this->open_id_connect_rest_api_config->session_crypt,
            $this->getRequestService()
        );
    }


    private function getRequestService() : RequestService
    {
        return RequestService::new(
            $this->getRestApi()
        );
    }


    private function getRestApi() : RestApi
    {
        return RestApi::new();
    }
}
