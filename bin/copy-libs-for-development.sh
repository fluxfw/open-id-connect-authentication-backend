#!/usr/bin/env sh

set -e

if [ -z `command -v copy-from-docker-image` ]; then
    echo "Please install flux-docker-utils"
    exit 1
fi

if [ -n "$CI_REGISTRY" ] && [ -n "$CI_PROJECT_NAMESPACE" ]; then
    image="$CI_REGISTRY/$CI_PROJECT_NAMESPACE/flux-open-id-connect-rest-api:latest"
else
    image="docker-registry.fluxpublisher.ch/flux-open-id-connect/rest-api:latest"
fi

(cd "`dirname "$0"`/.." && copy-from-docker-image "$image" /flux-open-id-connect-rest-api/libs libs)
