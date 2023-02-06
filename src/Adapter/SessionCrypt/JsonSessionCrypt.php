<?php

namespace FluxOpenIdConnectRestApi\Adapter\SessionCrypt;

use Exception;

trait JsonSessionCrypt
{

    public function decryptAsJson(?string $encrypted_session) : array
    {
        if (empty($encrypted_session)) {
            return [];
        }

        $session = json_decode($this->decrypt(
            $encrypted_session
        ), true);

        if (empty($session)) {
            throw new Exception("Json error: " . json_last_error_msg());
        }

        return $session;
    }


    public function encryptAsJson(?array $session) : ?string
    {
        if (empty($session)) {
            return null;
        }

        $encrypted_session = json_encode($session, JSON_UNESCAPED_SLASHES);

        if (empty($encrypted_session)) {
            throw new Exception("Json error: " . json_last_error_msg());
        }

        return $this->encrypt(
            $encrypted_session
        );
    }
}
