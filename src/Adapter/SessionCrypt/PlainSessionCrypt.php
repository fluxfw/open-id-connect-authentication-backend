<?php

namespace FluxOpenIdConnectRestApi\Adapter\SessionCrypt;

class PlainSessionCrypt implements SessionCrypt
{

    use JsonSessionCrypt;

    private function __construct()
    {

    }


    public static function new() : static
    {
        return new static();
    }


    public function decrypt(string $encrypted_session) : string
    {
        return $encrypted_session;
    }


    public function encrypt(string $session) : string
    {
        return $session;
    }
}
