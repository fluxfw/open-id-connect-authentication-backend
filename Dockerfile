FROM php:cli-alpine AS build

RUN (mkdir -p /flux-namespace-changer && cd /flux-namespace-changer && wget -O - https://github.com/flux-eco/flux-namespace-changer/releases/download/v2022-07-12-1/flux-namespace-changer-v2022-07-12-1-build.tar.gz | tar -xz --strip-components=1)

RUN (mkdir -p /build/flux-open-id-connect-rest-api/libs/flux-autoload-api && cd /build/flux-open-id-connect-rest-api/libs/flux-autoload-api && wget -O - https://github.com/flux-eco/flux-autoload-api/releases/download/v2022-07-12-1/flux-autoload-api-v2022-07-12-1-build.tar.gz | tar -xz --strip-components=1 && /flux-namespace-changer/bin/change-namespace.php . FluxAutoloadApi FluxOpenIdConnectRestApi\\Libs\\FluxAutoloadApi)

RUN (mkdir -p /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-api && cd /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-api && wget -O - https://github.com/flux-eco/flux-open-id-connect-api/releases/download/v2022-07-12-1/flux-open-id-connect-api-v2022-07-12-1-build.tar.gz | tar -xz --strip-components=1 && /flux-namespace-changer/bin/change-namespace.php . FluxOpenIdConnectApi FluxOpenIdConnectRestApi\\Libs\\FluxOpenIdConnectApi)

RUN (mkdir -p /build/flux-open-id-connect-rest-api/libs/flux-rest-api && cd /build/flux-open-id-connect-rest-api/libs/flux-rest-api && wget -O - https://github.com/flux-eco/flux-rest-api/releases/download/v2022-07-12-1/flux-rest-api-v2022-07-12-1-build.tar.gz | tar -xz --strip-components=1 && /flux-namespace-changer/bin/change-namespace.php . FluxRestApi FluxOpenIdConnectRestApi\\Libs\\FluxRestApi)

COPY . /build/flux-open-id-connect-rest-api

FROM php:cli-alpine

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

COPY --from=build /build /

ARG COMMIT_SHA
LABEL org.opencontainers.image.revision="$COMMIT_SHA"
