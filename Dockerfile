FROM node:19-alpine AS build

COPY bin/install-libraries.sh /build/flux-open-id-connect-authentication-backend/libs/flux-open-id-connect-authentication-backend/bin/install-libraries.sh
RUN /build/flux-open-id-connect-authentication-backend/libs/flux-open-id-connect-authentication-backend/bin/install-libraries.sh

RUN ln -s libs/flux-open-id-connect-authentication-backend/bin /build/flux-open-id-connect-authentication-backend/bin

COPY . /build/flux-open-id-connect-authentication-backend/libs/flux-open-id-connect-authentication-backend

FROM node:19-alpine

USER node:node

EXPOSE 443
EXPOSE 80

ENTRYPOINT ["/flux-open-id-connect-authentication-backend/bin/server.mjs"]

COPY --from=build /build /
