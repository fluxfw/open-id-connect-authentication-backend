ARG FLUX_AUTOLOAD_API_IMAGE=docker-registry.fluxpublisher.ch/flux-autoload/api
ARG FLUX_NAMESPACE_CHANGER_IMAGE=docker-registry.fluxpublisher.ch/flux-namespace-changer
ARG FLUX_OPEN_ID_CONNECT_API_IMAGE=docker-registry.fluxpublisher.ch/flux-open-id-connect/api
ARG FLUX_REST_API_IMAGE=docker-registry.fluxpublisher.ch/flux-rest/api

FROM $FLUX_AUTOLOAD_API_IMAGE:latest AS flux_autoload_api
FROM $FLUX_NAMESPACE_CHANGER_IMAGE:latest AS flux_autoload_api_build
ENV FLUX_NAMESPACE_CHANGER_FROM_NAMESPACE FluxAutoloadApi
ENV FLUX_NAMESPACE_CHANGER_TO_NAMESPACE FluxOpenIdConnectRestApi\\Libs\\FluxAutoloadApi
COPY --from=flux_autoload_api /flux-autoload-api /code
RUN change-namespace

FROM $FLUX_OPEN_ID_CONNECT_API_IMAGE:latest AS flux_open_id_connect_api
FROM $FLUX_NAMESPACE_CHANGER_IMAGE:latest AS flux_open_id_connect_api_build
ENV FLUX_NAMESPACE_CHANGER_FROM_NAMESPACE FluxOpenIdConnectApi
ENV FLUX_NAMESPACE_CHANGER_TO_NAMESPACE FluxOpenIdConnectRestApi\\Libs\\FluxOpenIdConnectApi
COPY --from=flux_open_id_connect_api /flux-open-id-connect-api /code
RUN change-namespace

FROM $FLUX_REST_API_IMAGE:latest AS flux_rest_api
FROM $FLUX_NAMESPACE_CHANGER_IMAGE:latest AS flux_rest_api_build
ENV FLUX_NAMESPACE_CHANGER_FROM_NAMESPACE FluxRestApi
ENV FLUX_NAMESPACE_CHANGER_TO_NAMESPACE FluxOpenIdConnectRestApi\\Libs\\FluxRestApi
COPY --from=flux_rest_api /flux-rest-api /code
RUN change-namespace

FROM alpine:latest AS build

COPY --from=flux_autoload_api_build /code /flux-open-id-connect-rest-api/libs/flux-autoload-api
COPY --from=flux_open_id_connect_api_build /code /flux-open-id-connect-rest-api/libs/flux-open-id-connect-api
COPY --from=flux_rest_api_build /code /flux-open-id-connect-rest-api/libs/flux-rest-api
COPY . /flux-open-id-connect-rest-api

FROM php:8.1-cli-alpine

LABEL org.opencontainers.image.source="https://github.com/flux-caps/flux-open-id-connect-rest-api"
LABEL maintainer="fluxlabs <support@fluxlabs.ch> (https://fluxlabs.ch)"

RUN apk add --no-cache libstdc++ && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev openssl-dev && \
    (mkdir -p /usr/src/php/ext/swoole && cd /usr/src/php/ext/swoole && wget -O - https://pecl.php.net/get/swoole | tar -xz --strip-components=1) && \
    docker-php-ext-configure swoole --enable-openssl --enable-swoole-curl --enable-swoole-json && \
    docker-php-ext-install -j$(nproc) swoole && \
    docker-php-source delete && \
    apk del .build-deps

USER www-data:www-data

EXPOSE 9501

ENTRYPOINT ["/flux-open-id-connect-rest-api/bin/server.php"]

COPY --from=build /flux-open-id-connect-rest-api /flux-open-id-connect-rest-api

ARG COMMIT_SHA
LABEL org.opencontainers.image.revision="$COMMIT_SHA"
