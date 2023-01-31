FROM php:8.2-cli-alpine AS build

COPY bin/install-libraries.sh /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-rest-api/bin/install-libraries.sh
RUN /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-rest-api/bin/install-libraries.sh

RUN ln -s libs/flux-open-id-connect-rest-api/bin /build/flux-open-id-connect-rest-api/bin

COPY . /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-rest-api

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
