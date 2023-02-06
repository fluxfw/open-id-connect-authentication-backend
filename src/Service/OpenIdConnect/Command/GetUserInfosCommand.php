<?php

namespace FluxOpenIdConnectRestApi\Service\OpenIdConnect\Command;

use Exception;
use FluxOpenIdConnectRestApi\Adapter\OpenId\OpenIdConfigDto;
use FluxOpenIdConnectRestApi\Adapter\SessionCrypt\SessionCrypt;
use FluxOpenIdConnectRestApi\Adapter\UserInfo\UserInfosDto;
use FluxOpenIdConnectRestApi\Service\Request\Port\RequestService;
use FluxRestApi\Adapter\Authorization\ParseHttp\ParseHttpAuthorization_;
use FluxRestApi\Adapter\Status\DefaultStatus;
use Throwable;

class GetUserInfosCommand
{

    private function __construct(
        private readonly OpenIdConfigDto $open_id_config,
        private readonly SessionCrypt $session_crypt,
        private readonly RequestService $request_service
    ) {

    }


    public static function new(
        OpenIdConfigDto $open_id_config,
        SessionCrypt $session_crypt,
        RequestService $request_service
    ) : static {
        return new static(
            $open_id_config,
            $session_crypt,
            $request_service
        );
    }


    public function getUserInfos(?string $encrypted_session) : array
    {
        $session = null;
        $user_infos = null;
        $encrypted_session_ = null;

        try {
            $session = $this->session_crypt->decryptAsJson(
                $encrypted_session
            );

            $authorization = $session["authorization"] ?? null;
            if (!empty($authorization)) {
                $user_infos = $this->getUserInfos_(
                    $authorization
                );
            }
        } catch (Throwable $ex) {
            if (str_contains($ex->getMessage(), DefaultStatus::_401->value) && !empty($refresh_token = $session["refresh_token"] ?? null)) {
                try {
                    $token = $this->request_service->request(
                        $this->open_id_config->token_endpoint,
                        null,
                        null,
                        [
                            "client_id"     => $this->open_id_config->provider_config->client_id,
                            "client_secret" => $this->open_id_config->provider_config->client_secret,
                            "refresh_token" => $refresh_token,
                            "grant_type"    => "refresh_token",
                            "redirect_uri"  => $this->open_id_config->provider_config->redirect_uri
                        ],
                        $this->open_id_config->provider_config->trust_self_signed_certificate
                    );

                    if (empty($token_type = $token["token_type"]) || empty($access_token = $token["access_token"])) {
                        throw new Exception("Invalid access token");
                    }

                    $session["authorization"] = $token_type . ParseHttpAuthorization_::SPLIT_SCHEMA_PARAMETERS . $access_token;
                    $session["refresh_token"] = $token["refresh_token"] ?? null;

                    $encrypted_session_ = $this->session_crypt->encryptAsJson(
                        $session
                    );
                    $user_infos = $this->getUserInfos_(
                        $session["authorization"]
                    );
                } catch (Throwable $ex) {
                    echo "Refresh token error: " . $ex . "\n";
                }
            } else {
                echo "Get user infos error: " . $ex . "\n";
            }
        }

        return [$user_infos, $encrypted_session_];
    }


    private function getUserInfos_(string $authorization) : ?UserInfosDto
    {
        $user_infos = $this->request_service->request(
            $this->open_id_config->user_info_endpoint,
            null,
            $authorization,
            null,
            $this->open_id_config->provider_config->trust_self_signed_certificate
        );

        if (!empty($user_infos)) {
            return UserInfosDto::new(
                $user_infos["sub"] ?? null,
                $user_infos["name"] ?? null,
                $user_infos["nickname"] ?? null,
                $user_infos["profile"] ?? null,
                $user_infos["picture"] ?? null,
                $user_infos["email"] ?? null
            );
        }

        return null;
    }
}
