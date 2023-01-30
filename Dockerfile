FROM php:8.2-cli-alpine AS build

RUN (mkdir -p /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-api && cd /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-api && wget -O - https://github.com/fluxfw/flux-open-id-connect-api/archive/refs/tags/v2023-01-30-1.tar.gz | tar -xz --strip-components=1)

RUN (mkdir -p /build/flux-open-id-connect-rest-api/libs/flux-rest-api && cd /build/flux-open-id-connect-rest-api/libs/flux-rest-api && wget -O - https://github.com/fluxfw/flux-rest-api/archive/refs/tags/v2023-01-30-1.tar.gz | tar -xz --strip-components=1)

COPY . /build/flux-open-id-connect-rest-api

FROM php:8.2-cli-alpine

RUN apk add --no-cache libstdc++ && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev openssl-dev && \
    (mkdir -p /usr/src/php/ext/swoole && cd /usr/src/php/ext/swoole && wget -O - https://pecl.php.net/get/swoole | tar -xz --strip-components=1) && \
    docker-php-ext-configure swoole --enable-openssl --enable-swoole-curl && \
    docker-php-ext-install -j$(nproc) swoole && \
    docker-php-source delete && \
    apk del .build-deps

USER www-data:www-data

EXPOSE 9501

ENTRYPOINT ["/flux-open-id-connect-rest-api/bin/server.php"]

COPY --from=build /build /

ARG COMMIT_SHA
LABEL org.opencontainers.image.revision="$COMMIT_SHA"
