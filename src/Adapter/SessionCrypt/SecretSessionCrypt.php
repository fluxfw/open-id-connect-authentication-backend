<?php

namespace FluxOpenIdConnectRestApi\Adapter\SessionCrypt;

use Exception;

class SecretSessionCrypt implements SessionCrypt
{

    use JsonSessionCrypt;

    private const SEPARATOR = "::";


    private function __construct(
        private readonly SessionCryptConfigDto $session_crypt_config
    ) {

    }


    public static function new(
        SessionCryptConfigDto $session_crypt_config
    ) : static {
        return new static(
            $session_crypt_config
        );
    }


    public function decrypt(string $encrypted_session) : string
    {
        $encrypted_session = base64_decode($encrypted_session);

        if (empty($encrypted_session) || substr_count($encrypted_session, static::SEPARATOR) !== 1) {
            throw new Exception("Invalid encrypted session");
        }

        [$value, $iv] = array_map("hex2bin", explode(static::SEPARATOR, $encrypted_session));

        if (empty($value) || empty($iv)) {
            throw new Exception("Invalid encrypted session");
        }

        $session = openssl_decrypt(json_encode($value, JSON_UNESCAPED_SLASHES), $this->session_crypt_config->method, hash("sha256", $this->session_crypt_config->secret), 0, $iv);

        if (empty($session)) {
            throw new Exception("OpenSSL error: " . openssl_error_string());
        }

        return $session;
    }


    public function encrypt(string $session) : string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->session_crypt_config->method));

        $value = openssl_encrypt($session, $this->session_crypt_config->method, hash("sha256", $this->session_crypt_config->secret), 0, $iv);

        if (empty($value)) {
            throw new Exception("OpenSSL error: " . openssl_error_string());
        }

        return base64_encode(implode(static::SEPARATOR, array_map("bin2hex", [$value, $iv])));
    }
}
