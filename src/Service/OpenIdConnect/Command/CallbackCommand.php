<?php

namespace FluxOpenIdConnectRestApi\Service\OpenIdConnect\Command;

use Exception;
use FluxOpenIdConnectRestApi\Adapter\OpenId\OpenIdConfigDto;
use FluxOpenIdConnectRestApi\Adapter\Route\RouteConfigDto;
use FluxOpenIdConnectRestApi\Adapter\SessionCrypt\SessionCrypt;
use FluxOpenIdConnectRestApi\Service\Request\Port\RequestService;
use FluxRestApi\Adapter\Authorization\ParseHttp\ParseHttpAuthorization_;
use Throwable;

class CallbackCommand
{

    private function __construct(
        private readonly OpenIdConfigDto $open_id_config,
        private readonly RouteConfigDto $route_config,
        private readonly SessionCrypt $session_crypt,
        private readonly RequestService $request_service
    ) {

    }


    public static function new(
        OpenIdConfigDto $open_id_config,
        RouteConfigDto $route_config,
        SessionCrypt $session_crypt,
        RequestService $request_service
    ) : static {
        return new static(
            $open_id_config,
            $route_config,
            $session_crypt,
            $request_service
        );
    }


    /**
     * @param string[] $query_params
     *
     * @return string[]
     */
    public function callback(?string $encrypted_session, array $query_params) : array
    {
        try {
            $session = $this->session_crypt->decryptAsJson(
                $encrypted_session
            );

            $session_state = $session["state"] ?? null;
            unset($session["state"]);

            $code_verifier = $session["code_verifier"] ?? null;
            unset($session["code_verifier"]);

            if (!empty($error_description = $query_params["error_description"] ?? null)) {
                throw new Exception("Get error description: " . $error_description);
            }

            if (!empty($error = $query_params["error"] ?? null)) {
                throw new Exception("Get error: " . $error);
            }

            if (empty($code = $query_params["code"] ?? null)) {
                throw new Exception("Invalid code");
            }

            if (empty($query_state = $query_params["state"] ?? null) || empty($session_state) || $session_state !== $query_state) {
                throw new Exception("Invalid state");
            }

            $data = [
                "client_id"     => $this->open_id_config->provider_config->client_id,
                "client_secret" => $this->open_id_config->provider_config->client_secret,
                "code"          => $code,
                "grant_type"    => "authorization_code",
                "redirect_uri"  => $this->open_id_config->provider_config->redirect_uri
            ];

            if ($this->open_id_config->provider_config->supports_pkce) {
                if (empty($code_verifier)) {
                    throw new Exception("Invalid code verifier");
                }

                $data["code_verifier"] = $code_verifier;
            }

            $token = $this->request_service->request(
                $this->open_id_config->token_endpoint,
                $data,
                null,
                $data,
                $this->open_id_config->provider_config->trust_self_signed_certificate
            );

            if (empty($token_type = $token["token_type"]) || empty($access_token = $token["access_token"])) {
                throw new Exception("Invalid access token");
            }

            $session["authorization"] = $token_type . ParseHttpAuthorization_::SPLIT_SCHEMA_PARAMETERS . $access_token;
            $session["refresh_token"] = $token["refresh_token"] ?? null;

            $redirect_url = $this->route_config->after_login_url;
        } catch (Throwable $ex) {
            echo "Callback error: " . $ex . "\n";

            $session = null;
            $redirect_url = null;
        }

        return [
            $this->session_crypt->encryptAsJson(
                $session
            ),
            $redirect_url
        ];
    }
}
