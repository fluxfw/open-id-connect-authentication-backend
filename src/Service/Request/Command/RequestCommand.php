<?php

namespace FluxOpenIdConnectRestApi\Service\Request\Command;

use Exception;
use FluxRestApi\Adapter\Api\RestApi;
use FluxRestApi\Adapter\Body\JsonBodyDto;
use FluxRestApi\Adapter\Body\Type\DefaultBodyType;
use FluxRestApi\Adapter\Client\ClientRequestDto;
use FluxRestApi\Adapter\Header\DefaultHeaderKey;
use FluxRestApi\Adapter\Method\DefaultMethod;

class RequestCommand
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
        $headers = [
            DefaultHeaderKey::ACCEPT->value     => DefaultBodyType::JSON->value
        ];

        if (!empty($authorization)) {
            $headers[DefaultHeaderKey::AUTHORIZATION->value] = $authorization;
        }

        if ($post_data !== null) {
            $method = DefaultMethod::POST;
            $post_data = JsonBodyDto::new(
                $post_data
            );
        } else {
            $method = DefaultMethod::GET;
        }

        $response = $this->rest_api->makeRequest(
            ClientRequestDto::new(
                $url,
                $method,
                $query_params,
                null,
                $headers,
                $post_data,
                null,
                null,
                true,
                true,
                false,
                $trust_self_signed_certificate
            )
        );

        if (empty($response = $response?->raw_body) || empty($response = json_decode($response, true))) {
            throw new Exception("Invalid response");
        }

        if (!empty($error_description = $response["error_description"] ?? null)) {
            throw new Exception("Request error description: " . $error_description);
        }

        if (!empty($error = $response["error"] ?? null)) {
            throw new Exception("Request error: " . $error);
        }

        if (!empty($message = $response["message"] ?? null)) {
            throw new Exception("Request message: " . $message);
        }

        return (array) $response;
    }
}
