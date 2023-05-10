FROM node:20-alpine AS build

COPY bin/install-libraries.sh /build/flux-open-id-connect-authentication-backend/libs/flux-open-id-connect-authentication-backend/bin/install-libraries.sh
RUN /build/flux-open-id-connect-authentication-backend/libs/flux-open-id-connect-authentication-backend/bin/install-libraries.sh

RUN ln -s libs/flux-open-id-connect-authentication-backend/bin /build/flux-open-id-connect-authentication-backend/bin

COPY . /build/flux-open-id-connect-authentication-backend/libs/flux-open-id-connect-authentication-backend

FROM node:20-alpine

USER node:node

ENTRYPOINT ["/flux-open-id-connect-authentication-backend/bin/server.mjs"]

COPY --from=build /build /
