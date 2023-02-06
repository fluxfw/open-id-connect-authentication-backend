<?php

namespace FluxOpenIdConnectRestApi\Adapter\Provider;

use SensitiveParameter;

class ProviderConfigDto
{

    private function __construct(
        public readonly string $url,
        public readonly string $client_id,
        public readonly string $client_secret,
        public readonly string $redirect_uri,
        public readonly string $scope,
        public readonly bool $supports_pkce,
        public readonly bool $trust_self_signed_certificate
    ) {

    }


    public static function new(
        string $url,
        string $client_id,
        #[SensitiveParameter] string $client_secret,
        string $redirect_uri,
        ?string $scope,
        ?bool $supports_pkce,
        ?bool $trust_self_signed_certificate = null
    ) : static {
        return new static(
            $url,
            $client_id,
            $client_secret,
            $redirect_uri,
            $scope ?? "openid profile email",
            $supports_pkce ?? true,
            $trust_self_signed_certificate ?? false
        );
    }


    public static function newFromEnv() : static
    {
        return static::new(
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_URL"],
            ($_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_CLIENT_ID"] ?? null) ??
            (($id_file = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_CLIENT_ID_FILE"] ?? null) !== null && file_exists($id_file) ? rtrim(file_get_contents($id_file) ?: "", "\n\r") : null),
            ($_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_CLIENT_SECRET"] ?? null) ??
            (($secret_file = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_CLIENT_SECRET_FILE"] ?? null) !== null && file_exists($secret_file) ? rtrim(file_get_contents($secret_file) ?: "", "\n\r")
                : null),
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_REDIRECT_URI"],
            $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_SCOPE"] ?? null,
            ($supports_proof_key_for_code_exchange = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_SUPPORTS_PKCE"] ?? null) !== null ? in_array($supports_proof_key_for_code_exchange, ["true", "1"])
                : null,
            ($trust_self_signed_certificate = $_ENV["FLUX_OPEN_ID_CONNECT_REST_API_PROVIDER_TRUST_SELF_SIGNED_CERTIFICATE"] ?? null) !== null ? in_array($trust_self_signed_certificate, ["true", "1"])
                : null
        );
    }
}
