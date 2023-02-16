FROM node:19-alpine AS build

COPY bin/install-libraries.sh /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-rest-api/bin/install-libraries.sh
RUN /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-rest-api/bin/install-libraries.sh

RUN ln -s libs/flux-open-id-connect-rest-api/bin /build/flux-open-id-connect-rest-api/bin

COPY . /build/flux-open-id-connect-rest-api/libs/flux-open-id-connect-rest-api

FROM node:19-alpine

USER node:node

EXPOSE 8080
EXPOSE 8443

ENTRYPOINT ["/flux-open-id-connect-rest-api/bin/server.mjs"]

COPY --from=build /build /
