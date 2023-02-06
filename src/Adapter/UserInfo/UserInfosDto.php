<?php

namespace FluxOpenIdConnectRestApi\Adapter\UserInfo;

class UserInfosDto
{

    private function __construct(
        public readonly ?string $sub,
        public readonly ?string $name,
        public readonly ?string $nickname,
        public readonly ?string $profile,
        public readonly ?string $picture,
        public readonly ?string $email
    ) {

    }


    public static function new(
        ?string $sub,
        ?string $name,
        ?string $nickname,
        ?string $profile,
        ?string $picture,
        ?string $email
    ) : static {
        return new static(
            $sub,
            $name,
            $nickname,
            $profile,
            $picture,
            $email
        );
    }
}
