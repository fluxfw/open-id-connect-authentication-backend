<?php

namespace FluxOpenIdConnectRestApi\Service\OpenIdConnect\Command;

use FluxOpenIdConnectRestApi\Adapter\OpenId\OpenIdConfigDto;
use FluxOpenIdConnectRestApi\Adapter\SessionCrypt\SessionCrypt;

class LoginCommand
{

    private function __construct(
        private readonly OpenIdConfigDto $open_id_config,
        private readonly SessionCrypt $session_crypt
    ) {

    }


    public static function new(
        OpenIdConfigDto $open_id_config,
        SessionCrypt $session_crypt
    ) : static {
        return new static(
            $open_id_config,
            $session_crypt
        );
    }


    /**
     * @return string[]
     */
    public function login() : array
    {
        $session = [];

        $session["state"] = $state = hash("sha256", rand() . microtime(true));

        $parameters = [
            "client_id"     => $this->open_id_config->provider_config->client_id,
            "redirect_uri"  => $this->open_id_config->provider_config->redirect_uri,
            "response_type" => "code",
            "state"         => $state,
            "scope"         => $this->open_id_config->provider_config->scope
        ];

        if ($this->open_id_config->provider_config->supports_pkce) {
            $session["code_verifier"] = $code_verifier = bin2hex(random_bytes(64));

            $parameters += [
                "code_challenge"        => rtrim(strtr(base64_encode(hash("sha256", $code_verifier, true)), "+/", "-_"), "="),
                "code_challenge_method" => "S256"
            ];
        }

        $authorize_url = $this->open_id_config->authorization_endpoint;
        $authorize_url .= (str_contains($authorize_url, "?") ? "&" : "?")
            . implode("&", array_map(fn(string $key, string $value) : string => $key . "=" . rawurlencode($value), array_keys($parameters), $parameters));

        return [
            $this->session_crypt->encryptAsJson(
                $session
            ),
            $authorize_url
        ];
    }
}
