<?php

namespace FluxOpenIdConnectRestApi\Adapter\SessionCrypt;

interface SessionCrypt
{

    public function decrypt(string $encrypted_session) : string;


    public function decryptAsJson(?string $encrypted_session) : array;


    public function encrypt(string $session) : string;


    public function encryptAsJson(?array $session) : ?string;
}
