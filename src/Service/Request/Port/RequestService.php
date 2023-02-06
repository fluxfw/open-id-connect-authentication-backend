<?php

namespace FluxOpenIdConnectRestApi\Service\Request\Port;

use FluxOpenIdConnectRestApi\Service\Request\Command\RequestCommand;
use FluxRestApi\Adapter\Api\RestApi;

class RequestService
{

    private function __construct(
        private readonly RestApi $rest_api
    ) {

    }


    public static function new(
        RestApi $rest_api
    ) : static {
        return new static(
            $rest_api
        );
    }


    public function request(string $url, ?array $query_params, ?string $authorization, ?array $post_data, ?bool $trust_self_signed_certificate) : array
    {
        return RequestCommand::new(
            $this->rest_api
        )
            ->request(
                $url,
                $query_params,
                $authorization,
                $post_data,
                $trust_self_signed_certificate
            );
    }
}
